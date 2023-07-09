<?php

use App\Models\Items\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryPrimaryCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::query()
                        ->select('currency_name', DB::raw('count(*) as count'))
                        ->groupBy('currency_name')
                        ->get();

        $countries->each(function ($country) {
            if ($country->count == 1) {
                Country::where('currency_name', $country->currency_name)
                    ->update(['is_primary_currency' => 1]);
            }
        });

        // USD
        Country::where('full_name', 'The United States of America')
            ->update(['is_primary_currency' => 1]);
            
        // XOF
        Country::where('full_name', 'the Republic of Benin')
            ->update(['is_primary_currency' => 1]);
            
        // AUD
        Country::where('full_name', 'The Commonwealth of Australia')
            ->update(['is_primary_currency' => 1]);
            
        // XCD
        Country::where('full_name', 'Saint Vincent and the Grenadines')
            ->update(['is_primary_currency' => 1]);

        // XAF
        Country::where('full_name', 'the Republic of Cameroon')
            ->update(['is_primary_currency' => 1]);

        // GBP
        Country::where('full_name', 'The United Kingdom of Great Britain and Northern Ireland')
            ->update(['is_primary_currency' => 1]);

        // NZD
        Country::where('full_name', 'New Zealand')
            ->update(['is_primary_currency' => 1]);

        // NOK
        Country::where('full_name', 'The Kingdom of Norway')
            ->update(['is_primary_currency' => 1]);

        // DKK
        Country::where('full_name', 'The Kingdom of Denmark')
            ->update(['is_primary_currency' => 1]);

        // XPF
        Country::where('full_name', 'New Caledonia')
            ->update(['is_primary_currency' => 1]);

        // ANG
        Country::where('full_name', 'Curaçao')
            ->update(['is_primary_currency' => 1]);

        // DZD
        Country::where('full_name', 'People\'s Democratic Republic of Algeria')
            ->update(['is_primary_currency' => 1]);

        // EUR
        Country::where('full_name', 'Reunion Island')
            ->update(['is_primary_currency' => 1]);
    }
}
