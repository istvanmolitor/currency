<?php

namespace Molitor\Currency\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Molitor\Currency\Models\Currency;

interface CurrencyRepositoryInterface
{
    public function makeCurrency(int|string|Currency|null $currency): ?Currency;

    public function getDefault(): ?Currency;

    public function setDefault(Currency $currency): void;

    public function getByCode(?string $code): ?Currency;

    public function getEnabledCurrencies(): Collection;

    public function delete(Currency $currency): bool;

    public function getAll(): Collection;

    public function getDefaultId(): ?int;

    public function getById(int $currency): ?Currency;

    public function getEnabledOptions(): array;
}
