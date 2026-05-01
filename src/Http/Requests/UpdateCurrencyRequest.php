<?php

namespace Molitor\Currency\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateCurrencyRequest',
    title: 'Update Currency Request',
    description: 'Data for updating a currency',
    required: ['code', 'name', 'symbol'],
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'USD', maxLength: 3),
        new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
        new OA\Property(property: 'symbol', type: 'string', example: '$'),
        new OA\Property(property: 'decimals', type: 'integer', example: 2),
        new OA\Property(property: 'decimal_separator', type: 'string', example: '.'),
        new OA\Property(property: 'thousands_separator', type: 'string', example: ','),
        new OA\Property(property: 'is_symbol_first', type: 'boolean', example: true),
        new OA\Property(property: 'is_enabled', type: 'boolean', example: true),
        new OA\Property(property: 'is_default', type: 'boolean', example: false),
    ]
)]
class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyId = $this->route('currency');

        return [
            'code' => [
                'required',
                'string',
                'max:3',
                Rule::unique('currencies', 'code')->ignore($currencyId),
            ],
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'decimals' => 'nullable|integer|min:0|max:8',
            'decimal_separator' => 'nullable|string|max:1',
            'thousands_separator' => 'nullable|string|max:1',
            'is_symbol_first' => 'nullable|boolean',
            'is_enabled' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];
    }
}
