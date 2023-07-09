<?php

use Illuminate\Database\Seeder;

class ExclusiveOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exclusive_offers')->insert([
            'label' => 'Sponsored',
            'item_name' => 'Sun Tours Snorkling',
            'promo' => '47% OFF',
            'image_url' => env('APP_URL') . '/img/promo_images/item.png',        
            'sponsor' => 'Quantas Australia',
            'logo' =>  env('APP_URL') . '/img/promo_images/logo.png',     
            'code' =>  "029138309102999910",
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('exclusive_offers')->insert([
            'label' => 'Sponsored',
            'item_name' => 'Sun Tours Snorkling',
            'promo' => '47% OFF',
            'image_url' => env('APP_URL') . '/img/promo_images/item.png',        
            'sponsor' => 'Quantas Australia',
            'logo' =>  env('APP_URL') . '/img/promo_images/logo.png',     
            'code' =>  "029138309102999910",    
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('exclusive_offers')->insert([
            'label' => 'Sponsored',
            'item_name' => 'Sun Tours Snorkling',
            'promo' => '47% OFF',
            'image_url' => env('APP_URL') . '/img/promo_images/item.png',        
            'sponsor' => 'Quantas Australia',
            'logo' =>  env('APP_URL') . '/img/promo_images/logo.png',     
            'code' =>  "029138309102999910",    
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
