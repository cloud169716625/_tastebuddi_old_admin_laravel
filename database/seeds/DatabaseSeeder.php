<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(ItemTagsTableSeeder::class);
        $this->call(ItemDetailsSeeder::class);
        $this->call(ExclusiveOffersSeeder::class);
        $this->call(WatchlistsTableSeeder::class);
        $this->call(RecommendationsSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ReportCategorySeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(CountriesImageSeeder::class);
        $this->call(CountryPrimaryCurrencySeeder::class);
    }
}
