<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Items\Country;
use Facades\App\Repositories\CurrencyRatesRepository;

class UpdateCachedRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cached rates daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currencies = Country::select('currency_code')
                            ->whereNotNull('currency_code')
                            ->groupBy('currency_code')->get();
        
        foreach($currencies as $base)
        {
            CurrencyRatesRepository::storeRates($base);
        }

    }
}
