<?php

namespace Molitor\Currency\Filament\Resources\CurrencyResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Molitor\Currency\Filament\Resources\CurrencyResource;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    public function getTitle(): string
    {
        return __('currency::common.edit');
    }

    protected function afterSave(): void
    {
        if ($this->record?->is_default) {
            app(CurrencyRepositoryInterface::class)->setDefault($this->record);
        }
    }
}
