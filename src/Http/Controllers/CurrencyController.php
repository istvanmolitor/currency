<?php

namespace Molitor\Currency\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Molitor\Admin\Traits\HasAdminFilters;
use Molitor\Currency\DataTables\CurrencyDataTable;
use Molitor\Currency\Http\Requests\StoreCurrencyRequest;
use Molitor\Currency\Http\Requests\UpdateCurrencyRequest;
use Molitor\Currency\Http\Resources\CurrencyResource;
use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;

class CurrencyController extends Controller
{
    use HasAdminFilters;

    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    public function index(CurrencyDataTable $dataTable): AnonymousResourceCollection
    {
        return $dataTable->getResponse();
    }

    public function select(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = max(1, min(500, (int) $request->input('per_page', 20)));
        $includeDisabled = $request->boolean('include_disabled', false);

        $query = Currency::query();

        if (! $includeDisabled) {
            $query->where('is_enabled', true);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('code', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('symbol', 'like', '%'.$search.'%');
            });
        }

        $currencies = $query
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => CurrencyResource::collection($currencies->items()),
            'meta' => [
                'current_page' => $currencies->currentPage(),
                'last_page' => $currencies->lastPage(),
                'per_page' => $currencies->perPage(),
                'total' => $currencies->total(),
            ],
            'filters' => [
                'search' => $search,
                'include_disabled' => $includeDisabled,
            ],
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([]);
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $currency = $this->currencyRepository->create(
            $validated['code'],
            $validated['name'],
            $validated['symbol'],
            $validated,
        );

        return response()->json([
            'data' => new CurrencyResource($currency),
            'message' => __('currency::currency.messages.created'),
        ], 201);
    }

    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    public function edit(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $validated = $request->validated();

        $currency = $this->currencyRepository->update(
            $currency,
            $validated['code'],
            $validated['name'],
            $validated['symbol'],
            $validated,
        );

        return response()->json([
            'data' => new CurrencyResource($currency),
            'message' => __('currency::currency.messages.updated'),
        ]);
    }

    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();

        return response()->json([
            'message' => __('currency::currency.messages.deleted'),
        ]);
    }
}
