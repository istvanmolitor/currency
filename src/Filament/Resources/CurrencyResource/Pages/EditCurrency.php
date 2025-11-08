<?php

namespace Molitor\Currency\Filament\Resources\CurrencyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Currency\Filament\Resources\CurrencyResource;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    public function getTitle(): string
    {
        return __('currency::common.edit');
    }
}
