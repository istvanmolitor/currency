<?php

namespace Molitor\Currency\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'is_enabled',
        'is_default',
        'code',
        'name',
        'symbol',
        'decimals',
        'decimal_separator',
        'thousands_separator',
        'is_symbol_first',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'decimals' => 'integer',
        'is_symbol_first' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $currency) {
            if ($currency->is_default) {
                throw new \RuntimeException(__('currency::currency.cannot_delete_default'));
            }
        });
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
