<?php

namespace Molitor\Currency\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Currency',
    title: 'Currency',
    description: 'Currency information',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'code', type: 'string', example: 'USD'),
        new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
        new OA\Property(property: 'symbol', type: 'string', example: '$'),
        new OA\Property(property: 'decimals', type: 'integer', example: 2),
        new OA\Property(property: 'decimal_separator', type: 'string', example: '.'),
        new OA\Property(property: 'thousands_separator', type: 'string', example: ','),
        new OA\Property(property: 'is_symbol_first', type: 'boolean', example: true),
        new OA\Property(property: 'is_enabled', type: 'boolean', example: true),
        new OA\Property(property: 'is_default', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'decimals' => $this->decimals,
            'decimal_separator' => $this->decimal_separator,
            'thousands_separator' => $this->thousands_separator,
            'is_symbol_first' => $this->is_symbol_first,
            'is_enabled' => $this->is_enabled,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
