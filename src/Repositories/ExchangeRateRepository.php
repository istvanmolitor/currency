<?php

namespace Molitor\Currency\Repositories;

use Molitor\Currency\Exceptions\ExchangeException;
use Molitor\Currency\Models\Currency;
use Molitor\Currency\Models\ExchangeRate;

class ExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    private array $cache = [];
    private ExchangeRate $exchangeRate;

    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository
    )
    {
        $this->exchangeRate = new ExchangeRate();
    }

    public function update(): void
    {
        $this->downloadHuf();
    }

    private function create(string $currency1, string $currency2, float $value): void
    {
        $this->exchangeRate->create([
            'currency_1_id' => $this->currencyRepository->getByCode($currency1)->id,
            'currency_2_id' => $this->currencyRepository->getByCode($currency2)->id,
            'value' => $value,
        ]);
    }

    public function downloadHuf(): void
    {
        $rows = simplexml_load_file('http://api.napiarfolyam.hu/?bank=kh');
        foreach ($rows->valuta->item as $row) {
            $this->create('HUF', $row->penznem, ($row->vetel + $row->eladas) / 2);
        }
    }

    public function getRate(Currency $sourceCurrency, Currency $destinationCurrency): float
    {
        if ($sourceCurrency->id === $destinationCurrency->id) {
            return 1;
        }

        if (isset($this->cache[$sourceCurrency->id][$destinationCurrency->id])) {
            return $this->cache[$sourceCurrency->id][$destinationCurrency->id];
        }

        $exchangeRate = $this->exchangeRate
            ->where('currency_1_id', $sourceCurrency->id)
            ->where('currency_2_id', $destinationCurrency->id)
            ->orderBy('created_at', 'desc')
            ->select('value')
            ->first();

        if ($exchangeRate) {
            $this->cache[$sourceCurrency->id][$destinationCurrency->id] = 1 / $exchangeRate->value;
            return $this->cache[$sourceCurrency->id][$destinationCurrency->id];
        }

        $exchangeRate = $this->exchangeRate
            ->where('currency_2_id', $sourceCurrency->id)
            ->where('currency_1_id', $destinationCurrency->id)
            ->orderBy('created_at', 'desc')
            ->select('value')
            ->first();

        if ($exchangeRate) {
            $this->cache[$sourceCurrency->id][$destinationCurrency->id] = $exchangeRate->value;
            return $this->cache[$sourceCurrency->id][$destinationCurrency->id];
        }

        throw new ExchangeException();
    }

    public function exchange(float $price, Currency $sourceCurrency, Currency $destinationCurrency): float
    {
        return round($price * $this->getRate($sourceCurrency, $destinationCurrency), 2);
    }
}
