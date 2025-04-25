<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PriceUpdater;

class UpdateGadgetPrices extends Command
{
    protected $signature = 'prices:update';
    protected $description = 'Оновлює ціни гаджетів з eBay та AliExpress';

    private PriceUpdater $priceUpdater;

    public function __construct(PriceUpdater $priceUpdater)
    {
        parent::__construct();
        $this->priceUpdater = $priceUpdater;
    }

    public function handle()
    {
        $this->info("🚀 Оновлення цін почалося...");
        $result = $this->priceUpdater->updatePrices();
        $this->info($result);
    }
}
