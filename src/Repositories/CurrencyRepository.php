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

    public function getDefault(): Currency|null
    {
        return $this->getByCode('HUF');
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
        return $this->currency->where('enabled', 1)->orderBy('code')->get();
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
}
