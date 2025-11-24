<?php

namespace Molitor\Currency\Filament\Components;

use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Filament\Forms;

class EnabledCurrencySelect
{
    public static function make(string $name) {
        /** @var CurrencyRepositoryInterface $currencyRepository */
        $currencyRepository = app(CurrencyRepositoryInterface::class);
        return Forms\Components\Select::make($name)
            ->label(__('product::common.currency'))
            ->options($currencyRepository->getEnabledOptions())
            ->default($currencyRepository->getDefaultId())
            ->searchable();
    }
}
