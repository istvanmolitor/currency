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
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'currency');

        // Load API routes with /api prefix
        $this->app->make(\Illuminate\Routing\Router::class)
            ->prefix('api')
            ->group(__DIR__.'/../routes/api.php');
    }

    public function register()
    {
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(ExchangeRateRepositoryInterface::class, ExchangeRateRepository::class);
    }
}
