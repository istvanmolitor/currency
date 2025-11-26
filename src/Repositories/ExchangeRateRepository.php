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

    private function create(int|string|Currency|null $sourceCurrency, int|string|Currency|null $destinationCurrency, float $value): void
    {
        $this->exchangeRate->create([
            'currency_1_id' => $this->currencyRepository->makeId($sourceCurrency),
            'currency_2_id' => $this->currencyRepository->makeId($destinationCurrency),
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

    protected function getValue(Currency $sourceCurrency, Currency $destinationCurrency): float|null
    {
        if($sourceCurrency->id === $destinationCurrency->id) {
            return 1;
        }
        $exchangeRate = $this->exchangeRate->where(function ($query) use ($sourceCurrency, $destinationCurrency) {
            $query->where('currency_1_id', $sourceCurrency->id)
                ->where('currency_2_id', $destinationCurrency->id);
        })->orWhere(function ($query) use ($sourceCurrency, $destinationCurrency) {
            $query->where('currency_1_id', $destinationCurrency->id)
                ->where('currency_2_id', $sourceCurrency->id);
        })->orderBy('created_at', 'desc')->first();

        if (!$exchangeRate) {
            return null;
        }

        if($sourceCurrency->id === $exchangeRate->currency_1_id) {
            return 1 / $exchangeRate->value;
        }

        return $exchangeRate->value;
    }

    public function getRelatedIds(int $currencyId): array
    {
        $currencyIds = [];
        $this->exchangeRate
            ->where('currency_1_id', $currencyId)
            ->orWhere('currency_2_id', $currencyId)
            ->groupBy('currency_1_id', 'currency_2_id')
            ->select('currency_1_id', 'currency_2_id')
            ->get()->each(function ($item) use (&$currencyIds, $currencyId) {
                if ($item->currency_1_id !== $currencyId) {
                    $currencyIds[] = $item->currency_1_id;
                }
                if ($item->currency_2_id !== $currencyId) {
                    $currencyIds[] = $item->currency_2_id;
                }
            });
        return array_unique($currencyIds);
    }

    public function getGeteId(int $currencyId1, int $currencyId2): int|null
    {
        $intersect =  array_intersect($this->getRelatedIds($currencyId1), $this->getRelatedIds($currencyId2));
        return array_first($intersect);
    }

    public function getRate(Currency $sourceCurrency, Currency $destinationCurrency): float
    {
        if (!isset($this->cache[$sourceCurrency->id][$destinationCurrency->id])) {
            $rate = $this->getValue($sourceCurrency, $destinationCurrency);
            if($rate === null) {
                throw new ExchangeException("No exchange rate found for {$sourceCurrency->code} to {$destinationCurrency->code}");
            }
            $this->cache[$sourceCurrency->id][$destinationCurrency->id] = $rate;
        }

        return $this->cache[$sourceCurrency->id][$destinationCurrency->id];
    }

    public function exchange(float $price, Currency $sourceCurrency, Currency $destinationCurrency): float
    {
        try {
            return $price * $this->getRate($sourceCurrency, $destinationCurrency);
        }
        catch (ExchangeException $e) {
            $gateId = $this->getGeteId($sourceCurrency->id, $destinationCurrency->id);
            if($gateId === null) {
                throw $e;
            }

            $gate = $this->currencyRepository->getById($gateId);

            return $price * $this->getRate($sourceCurrency, $gate) * $this->getRate($gate, $destinationCurrency);
        }
    }
}
