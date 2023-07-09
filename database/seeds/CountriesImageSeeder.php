<?php

use App\Enums\MediaCollectionType;
use App\Models\Items\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CountriesImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::all()->each(function (Country $country) {
            $flagPath = 'assets/flags/' . $country->country_alpha_code_2 . '.png';
            $backgroundPath = 'assets/country_backgrounds/placeholder.png';

            if (! Storage::disk('local')->exists($flagPath)) return;
            
            $country->addMediaFromBase64(base64_encode(Storage::disk('local')->get($flagPath)))
                    ->usingFileName("{$country->country_alpha_code_2}_flag" . microtime(true) . ".png")
                    ->toMediaCollection(MediaCollectionType::COUNTRY_FLAG_IMAGE);
                    
            if (! Storage::disk('local')->exists($backgroundPath)) return;

            $country->addMediaFromBase64(base64_encode(Storage::disk('local')->get($backgroundPath)))
                    ->usingFileName("{$country->country_alpha_code_2}_background" . microtime(true) . ".png")
                    ->toMediaCollection(MediaCollectionType::COUNTRY_BACKGROUND);
        });

        $selectedCountries = ['fr', 'th', 'in', 'id'];

        Country::whereIn('country_alpha_code_2', $selectedCountries)
                ->get()
                ->each(function (Country $country) {
                    $flagPath = 'assets/flags/' . $country->country_alpha_code_2 . '.png';
                    $backgroundPath = 'assets/country_backgrounds/' . $country->country_alpha_code_2 . '.png';

                    if (! Storage::disk('local')->exists($flagPath)) return;
            
                    $country->addMediaFromBase64(base64_encode(Storage::disk('local')->get($flagPath)))
                            ->usingFileName("{$country->country_alpha_code_2}_flag" . microtime(true) . ".png")
                            ->toMediaCollection(MediaCollectionType::COUNTRY_FLAG_IMAGE);
                            
                    if (! Storage::disk('local')->exists($backgroundPath)) return;
        
                    $country->addMediaFromBase64(base64_encode(Storage::disk('local')->get($backgroundPath)))
                            ->usingFileName("{$country->country_alpha_code_2}_background" . microtime(true) . ".png")
                            ->toMediaCollection(MediaCollectionType::COUNTRY_BACKGROUND);
                });
    }
}
