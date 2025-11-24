<?php

namespace Molitor\Currency\Filament\Resources\CurrencyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Molitor\Currency\Filament\Resources\CurrencyResource;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;

    public function getTitle(): string
    {
        return __('currency::common.create');
    }

    protected function afterCreate(): void
    {
        if ($this->record?->is_default) {
            app(CurrencyRepositoryInterface::class)->setDefault($this->record);
        }
    }
}
