<?php

namespace Molitor\Currency\Services;

use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class Price
{
    public float $price;

    public Currency $currency;
    protected null|int $decimals = null;

    public function __construct(float $price, null|int|string|Currency $currency)
    {
        $this->price = $price;
        $this->currency = $this->makeCurrency($currency);
    }

    protected function makeCurrency(int|string|Currency|null $currency): Currency|null
    {
        if($currency instanceof Currency) {
            return $currency;
        }
        /** @var CurrencyRepositoryInterface $currencyRepository */
        $currencyRepository = app(CurrencyRepositoryInterface::class);
        return $currencyRepository->makeCurrency($currency);
    }

    public function __toString(): string
    {
        $decimals = $this->decimals ?? $this->currency->decimals;
        $number = number_format($this->price, $decimals, $this->currency->decimal_separator, $this->currency->thousands_separator);
        if($this->currency->is_symbol_first) {
            return $this->currency->symbol . ' ' . $number;
        }
        return $number . ' ' . $this->currency->symbol;
    }

    public function exchange(int|string|Currency|null $currency): Price
    {
        $currency = $this->makeCurrency($currency);
        if($this->currency->id === $currency->id) {
            return $this;
        }

        /** @var ExchangeRateRepositoryInterface $exchangeRateRepository */
        $exchangeRateRepository = app(ExchangeRateRepositoryInterface::class);
        $price = $exchangeRateRepository->exchange($this->price, $this->currency, $currency);
        return new Price($price, $currency);
    }

    public function exchangeDefault(): Price
    {
        /** @var CurrencyRepositoryInterface $currencyRepository */
        $currencyRepository = app(CurrencyRepositoryInterface::class);
        return $this->exchange($currencyRepository->getDefault());
    }

    public function multiple(float $factor): Price
    {
        return new Price($this->price * $factor, $this->currency);
    }

    public function addition(Price $price): Price
    {
        $price = $price->exchange($this->currency);
        return new Price($this->price + $price->price, $this->currency);
    }
}
