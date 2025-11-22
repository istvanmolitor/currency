<?php

namespace Molitor\Currency\Filament\Resources\ExchangeRateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Molitor\Currency\Filament\Resources\ExchangeRateResource;

class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    public function getTitle(): string
    {
        return __('currency::common.create');
    }
}
