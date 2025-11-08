<?php

namespace Molitor\Currency\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Currency\Models\Currency;

interface CurrencyRepositoryInterface
{
    public function getDefault(): Currency|null;

    public function getByCode(string $code): Currency|null;

    public function getEnabledCurrencies(): Collection;

    public function delete(Currency $currency): bool;

    public function getAll(): Collection;

    public function getDefaultId(): int|null;
}
