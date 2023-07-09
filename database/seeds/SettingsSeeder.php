<?php

use App\Enums\PagesType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'name' => PagesType::PRIVACY
        ]);

        Setting::create([
            'name' => PagesType::TERMS_OF_SERVICE
        ]);
    }
}
