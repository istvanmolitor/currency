<?php

namespace Molitor\Currency\Services;

use Molitor\Currency\Models\Currency;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class PriceService
{
    public function __construct(
        protected CurrencyRepositoryInterface $currencyRepository,
        protected ExchangeRateRepositoryInterface $exchangeRateRepository
    )
    {
        $this->currency = $this->currencyRepository->getDefault();
    }

    protected Currency $currency;

    protected function makeCurrency(int|string|Currency|null $currency): Currency|null
    {
        if($currency instanceof Currency) {
            return $currency;
        } elseif (is_string($currency)) {
            return $this->currencyRepository->getByCode($currency);
        } elseif (is_int($currency)) {
            return $this->currencyRepository->getById($currency);
        } else{
            return $this->currencyRepository->getDefault();
        }
    }

    public function price(float $price, int|string|Currency|null $currency): float
    {
        $currency = $this->makeCurrency($currency);
        if (!$currency) {
            return 0;
        }

        $exchangeRate = $this->exchangeRateRepository->getByCurrency($currency);

        if (!$exchangeRate) {
            return $price;
        }

        return round($price * $exchangeRate->rate, 2);
    }

    public function getString(float $price, int|string|Currency|null $currency): string
    {
        $currency = $this->makeCurrency($currency);
        if (!$currency) {
            return '-';
        }


    }
}
