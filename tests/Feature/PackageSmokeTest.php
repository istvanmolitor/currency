<?php

namespace Molitor\Currency\Tests\Feature;

use Molitor\Currency\Providers\CurrencyServiceProvider;
use Tests\TestCase;

class PackageSmokeTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        $this->assertTrue(class_exists(CurrencyServiceProvider::class));
        $this->assertTrue($this->app->providerIsLoaded(CurrencyServiceProvider::class));
    }
}

