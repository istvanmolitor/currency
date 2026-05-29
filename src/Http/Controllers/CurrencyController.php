<?php

namespace Molitor\Currency\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Molitor\Admin\Traits\HasAdminFilters;
use Molitor\Currency\Http\Requests\StoreCurrencyRequest;
use Molitor\Currency\Http\Requests\UpdateCurrencyRequest;
use Molitor\Currency\Http\Resources\CurrencyResource;
use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use OpenApi\Attributes as OA;

class CurrencyController extends Controller
{
    use HasAdminFilters;

    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    #[OA\Get(
        path: '/api/admin/currency/currencies',
        summary: 'List all currencies',
        tags: ['Currencies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Currency')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Currency::query();
        $currencies = $this->applyAdminFilters($query, $request, ['code', 'name'])
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'data' => CurrencyResource::collection($currencies->items()),
            'meta' => [
                'current_page' => $currencies->currentPage(),
                'last_page' => $currencies->lastPage(),
                'per_page' => $currencies->perPage(),
                'total' => $currencies->total(),
            ],
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/currency/currencies/select',
        summary: 'Search currencies for select inputs',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'include_disabled', in: 'query', required: false, schema: new OA\Schema(type: 'boolean', default: false)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Currency')
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
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

    #[OA\Get(
        path: '/api/admin/currency/currencies/create',
        summary: 'Show form for creating a currency',
        tags: ['Currencies'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
        ]
    )]
    public function create(): JsonResponse
    {
        return response()->json([]);
    }

    #[OA\Post(
        path: '/api/admin/currency/currencies',
        summary: 'Store a new currency',
        tags: ['Currencies'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreCurrencyRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Currency'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/admin/currency/currencies/{currency}',
        summary: 'Display a specific currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'currency', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Currency'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    #[OA\Get(
        path: '/api/admin/currency/currencies/{currency}/edit',
        summary: 'Show form for editing a currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'currency', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function edit(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    #[OA\Put(
        path: '/api/admin/currency/currencies/{currency}',
        summary: 'Update a currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'currency', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCurrencyRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Currency'),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/admin/currency/currencies/{currency}',
        summary: 'Delete a currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'currency', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();

        return response()->json([
            'message' => __('currency::currency.messages.deleted'),
        ]);
    }
}
