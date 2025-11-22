<?php

namespace Molitor\Currency\Filament\Resources\ExchangeRateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Currency\Filament\Resources\ExchangeRateResource;

class EditExchangeRate extends EditRecord
{
    protected static string $resource = ExchangeRateResource::class;

    public function getTitle(): string
    {
        return __('currency::common.edit');
    }
}
