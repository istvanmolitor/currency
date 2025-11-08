<?php

namespace Molitor\Currency\Providers;

use Illuminate\Support\ServiceProvider;
use Molitor\Currency\Repositories\CurrencyRepository;
use Molitor\Currency\Repositories\CurrencyRepositoryInterface;
use Molitor\Currency\Repositories\ExchangeRateRepository;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class CurrencyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'currency');
    }

    public function register()
    {
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(ExchangeRateRepositoryInterface::class, ExchangeRateRepository::class);
    }
}
