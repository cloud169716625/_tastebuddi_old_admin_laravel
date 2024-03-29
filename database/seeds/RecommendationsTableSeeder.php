<?php

use Illuminate\Database\Seeder;

class RecommendationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('recommendations')->insert([
            'recommended_price' => 123.45,
            'user_id' => 1,
            'item_id' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('recommendations')->insert([
            'recommended_price' => 123.45,
            'user_id' => 1,
            'item_id' => 2,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('recommendations')->insert([
            'recommended_price' => 123.45,
            'user_id' => 1,
            'item_id' => 3,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }
}
