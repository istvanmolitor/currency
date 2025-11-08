<?php

namespace Molitor\Currency\Filament\Resources\CurrencyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Molitor\Currency\Filament\Resources\CurrencyResource;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;

    public function getTitle(): string
    {
        return __('currency::common.create');
    }
}
