<?php

namespace Molitor\Currency\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'enabled',
        'code',
        'name',
        'symbol',
    ];

    public function __toString()
    {
        return $this->code;
    }
}
