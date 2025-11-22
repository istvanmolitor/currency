<?php

namespace Molitor\Currency\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'exchange_rates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'currency_1_id',
        'currency_2_id',
        'value',
    ];

    const UPDATED_AT = null;

    public function currency1()
    {
        return $this->belongsTo(Currency::class, 'currency_1_id');
    }

    public function currency2()
    {
        return $this->belongsTo(Currency::class, 'currency_2_id');
    }
}
