<?php

use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Currency\Services\Price;

if (! function_exists('price')) {
    function price(float $amount, int|string|Currency|null $currency): Price
    {
        /** @var CurrencyRepositoryInterface $currencyRepository */
        $currencyRepository = app(CurrencyRepositoryInterface::class);

        $price = new Price($amount, $currency);
        return $price->exchange($currencyRepository->getDefault());
    }
}
