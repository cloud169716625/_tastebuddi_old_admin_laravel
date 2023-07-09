<?php

namespace App\Repositories;

use Ranium\Fixerio\Client;
use Carbon\Carbon;
use Cache;

class CurrencyRatesRepository
{
    const CACHE_KEY = 'RATES';
   
    public function getCacheRates($base)
    {
        $key = self::CACHE_KEY .".$base";
        return Cache::remember($key, Carbon::now()->endOfDay(), function () use ($base) {
            $accessKey = config('app.fixer')['ACCESS_KEY'];
            $secure  = config('app.fixer')['ACCOUNT_SECURE'];
            $config = [];
            $fixerio = Client::create($accessKey, $secure, $config);
            
            $conversion_rates = $fixerio->latest(
                [
                    'base' => $base
                ]
            );

            $rates = array();
            $index = 0;
            foreach ($conversion_rates['rates'] as $key => $value) {
                $rates[$index]['currency_code'] = $key;
                $rates[$index]['conversion_value'] = $value;
                $index++;
            }

            return $rates;
        });
    }

    public function convertRates($base, $to)
    {
        $key = self::CACHE_KEY .".$base";
        $rates = $this->getCacheRates($base);

        $index = array_search($to, array_column($rates, 'currency_code'));
        $conversion_rate = $rates[$index];

        return $conversion_rate['conversion_value'];
    }
}
