<?php

namespace Molitor\Currency\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Currency\Models\Currency;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    private Currency $currency;
    private array $cache = [];

    public function __construct()
    {
        $this->currency = new Currency();
    }

    public function makeCurrency(int|string|Currency|null $currency): Currency|null
    {
        if($currency instanceof Currency) {
            return $currency;
        }
        if (is_string($currency)) {
            return $this->getByCode($currency);
        }
        if (is_int($currency)) {
            return $this->getById($currency);
        }
        return $this->getDefault();
    }

    public function makeId(int|string|Currency|null $currency): int|null
    {
        return $this->makeCurrency($currency)->id;
    }

    public function getDefault(): Currency|null
    {
        return $this->currency->where('is_default', 1)->first();
    }

    public function setDefault(Currency $currency): void
    {
        $currency->is_default = true;
        $currency->save();
        $this->currency->where('id', '!=', $currency->id)->update(['is_default' => false]);
    }

    public function getByCode(string|null $code): Currency|null
    {
        if($code === null) {
            return $this->getDefault();
        }
        if (!array_key_exists($code, $this->cache)) {
            $this->cache[$code] = $this->currency->where('code', $code)->first();
        }
        return $this->cache[$code];
    }

    public function getEnabledCurrencies(): Collection
    {
        return $this->currency->where('is_enabled', 1)->orderBy('code')->get();
    }

    public function delete(Currency $currency): bool
    {
        return $currency->delete();
    }

    public function getAll(): Collection
    {
        return $this->currency->orderBy('name')->get();
    }

    public function getDefaultId(): int|null
    {
        return $this->getDefault()?->id;
    }

    public function getById(int $currency): Currency|null
    {
        return $this->currency->where('id', $currency)->first();
    }

    public function getEnabledOptions(): array
    {
        return $this->getEnabledCurrencies()->pluck('code', 'id')->toArray();
    }
}
