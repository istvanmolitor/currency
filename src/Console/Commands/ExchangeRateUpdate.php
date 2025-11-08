<?php

namespace Molitor\Currency\Console\Commands;

use Illuminate\Console\Command;
use Molitor\Currency\Repositories\ExchangeRateRepositoryInterface;

class ExchangeRateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rate:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Árfolyam frissítése';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private ExchangeRateRepositoryInterface $exchangeRateRepository
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->exchangeRateRepository->update();
        return 0;
    }
}
