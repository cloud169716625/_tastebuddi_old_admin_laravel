<?php

use Illuminate\Database\Seeder;

class ItemTagsTableSeeder extends Seeder
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
            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 3,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 3,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;
                        
            DB::table('item_tags')->insert([
                'item_id' => $i,
                'tag_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            $i++;

        // }
    }
}
