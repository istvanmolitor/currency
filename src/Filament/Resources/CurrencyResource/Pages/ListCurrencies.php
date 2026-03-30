<?php

namespace Molitor\Currency\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Molitor\Currency\Filament\Resources\CurrencyResource;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    public function getBreadcrumb(): string
    {
        return __('currency::common.list');
    }

    public function getTitle(): string
    {
        return __('currency::currency.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('currency::currency.create'))
                ->icon('heroicon-o-plus'),
        ];
    }
}
