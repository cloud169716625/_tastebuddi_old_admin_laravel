<?php

use Illuminate\Database\Seeder;

class RecommendationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 1;
        // while($i < 131){
            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 9, 
                'recommended_price' => 920,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 3, 
                'recommended_price' => 800,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 1, 
                'recommended_price' => 850,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 2, 
                'recommended_price' => 500,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 3, 
                'recommended_price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 9, 
                'recommended_price' => 500,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 1, 
                'recommended_price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 2, 
                'recommended_price' => 480,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 1, 
                'recommended_price' => 400,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 3, 
                'recommended_price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;
            
            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 4, 
                'recommended_price' => 120,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 5, 
                'recommended_price' => 150,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 4, 
                'recommended_price' => 180,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 5, 
                'recommended_price' => 190,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 6, 
                'recommended_price' => 110,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 10, 
                'recommended_price' => 150,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 11, 
                'recommended_price' => 90,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 12, 
                'recommended_price' => 80,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 7, 
                'recommended_price' => 664.28,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 13, 
                'recommended_price' => 1583.45,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 14, 
                'recommended_price' => 4698.68,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 8, 
                'recommended_price' => 2349.34,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 15, 
                'recommended_price' => 240,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 2,
                'item_id' => $i,
                'location_id' => 16, 
                'recommended_price' => 8,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('recommendations')->insert([
                'user_id' => 1,
                'item_id' => $i,
                'location_id' => 17, 
                'recommended_price' => 18,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

        // }
    }
}
