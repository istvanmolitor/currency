<?php

namespace Molitor\Currency\Events;

use Molitor\Currency\Models\Currency;

class DefaultCurrencyChanged
{
    public function __construct(
        public readonly Currency $currency,
        public readonly Currency|null $previousCurrency = null,
    ) {}
}
