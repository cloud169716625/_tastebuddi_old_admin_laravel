<?php

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $i = 0;
//        while($i < 10){
            DB::table('items')->insert([
                'item_name' => "Traditional Street Meal - Pad Thai",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 1 . '.png',
                'city_id' => 1936,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Traditional Street Meal - Sticky rice mango",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 2 . '.png',
                'city_id' => 1936,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Local Thai King Fisher Beer",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 3 . '.png',
                'city_id' => 1936,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Thai Peanut Chicken Ramen",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 4 . '.png',
                'city_id' => 1936,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Pierogi",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 5 . '.png',
                'city_id' => 694,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Bundz Cheese - Polish Sheep Milk Cheese",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 6 . '.png',
                'city_id' => 694,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Indian Traditional Street Curry",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 7 . '.png',
                'city_id' => 2544,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Local Indian Beer Kingfisher",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 8 . '.png',
                'city_id' => 2544,
                'category_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Typical - 3 Star Hotel Room in City Centre",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 9 . '.png',
                'city_id' => 1936,
                'category_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Luxury - 5 Star Hotel Stay at Resort",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 10 . '.png',
                'city_id' => 1936,
                'category_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Erawan National Park Admission",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 11 . '.png',
                'city_id' => 1936,
                'category_id' => 3,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Notre Dame - General Admission",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 12 . '.png',
                'city_id' => 694,
                'category_id' => 3,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('items')->insert([
                'item_name' => "Paris Hop - On Hop - Off Seine River Sightseeing",
                // 'image_url' => env('APP_URL') . '/img/item_images/item_' . 13 . '.png',
                'city_id' => 694,
                'category_id' => 3,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

//            $i++;
//        }

    }
}
