<?php

namespace Molitor\Currency\Repositories;

use Molitor\Currency\Models\Currency;

interface ExchangeRateRepositoryInterface
{
    public function update(): void;

    public function downloadHuf(): void;

    public function getRate(Currency $sourceCurrency, Currency $destinationCurrency): float;

    public function exchange(float $price, Currency $sourceCurrency, Currency $destinationCurrency): float;
}
