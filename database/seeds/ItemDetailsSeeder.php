<?php

use Illuminate\Database\Seeder;

class ItemDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 1;
        // while($i < 13){
            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 9, 
                'price' => 920,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 3, 
                'price' => 800,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 1, 
                'price' => 850,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 2, 
                'price' => 500,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 3, 
                'price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 9, 
                'price' => 500,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 1, 
                'price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 2, 
                'price' => 480,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 1, 
                'price' => 400,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 3, 
                'price' => 450,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            $i++;
            
            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 4, 
                'price' => 120,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 5, 
                'price' => 150,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 4, 
                'price' => 180,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 5, 
                'price' => 190,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 6, 
                'price' => 110,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 10, 
                'price' => 150,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 11, 
                'price' => 90,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 12, 
                'price' => 80,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 7, 
                'price' => 664.28,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 13, 
                'price' => 1583.45,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 14, 
                'price' => 4698.68,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 8, 
                'price' => 2349.34,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 15, 
                'price' => 240,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 16, 
                'price' => 8,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

            DB::table('item_details')->insert([
                'item_id' => $i,
                'location_id' => 17, 
                'price' => 18,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            
            $i++;

        // }s
    }
}
