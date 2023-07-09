<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            'place_id' => "ChIJxU5YeBaZ4jARvm8EVZYGjGk",
            'location' => "Thip Samai Pad Thai",
            'address' => "313 Mahachai Road, Old City",
            'city_id' => 1936,
            'lat_coordinate' => 13.752868,
            'lng_coordinate' => 100.504846,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJmbE4etuj4jAR4f39xOMOrJE",
            'location' => "Chin Chin",
            'address' => "120 Mahachai Road, Old City",
            'city_id' => 1936,
            'lat_coordinate' => 13.748143,
            'lng_coordinate' => 100.501956,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJDf7QWt2e4jAR_P65R6dS78A",
            'location' => "Goji Kitchen + Bar",
            'address' => "60 Mahachai Road, Old City",
            'city_id' => 1936,
            'lat_coordinate' => 13.730539,
            'lng_coordinate' => 100.565486,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]); 

        DB::table('locations')->insert([
            'place_id' => "ChIJnSuBEy1w5kcRPCeTZOBKJvc",
            'location' => "Fromagerie Quatrehomme",
            'address' => "62 Rue de Sèvres, 75007",
            'city_id' => 694,
            'lat_coordinate' => 48.848314,
            'lng_coordinate' => 2.319593,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJw0uHqmty5kcRkMHZqY2RlmE",
            'location' => "Fromagerie de Paris Lefebvre",
            'address' => "229 Rue de Charenton, 75012",
            'city_id' => 694,
            'lat_coordinate' => 48.838164,
            'lng_coordinate' => 2.390613,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJaRqQP5i0bTkR_lbqtdKpIdk",
            'location' => "Kebabs & Curries",
            'address' => "Chitrakoot Marg, Gangaram Nagar, Chitrakoot, Jaipur, Rajasthan 302021, India",
            'city_id' => 2544,
            'lat_coordinate' => 26.9028038,
            'lng_coordinate' => 75.7387162,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJN2vWa8Cf4jAROc34xmnn700",
            'location' => "Klassique Sukhumvit",
            'address' => "Sukhumvit Road, Phra Khanong, Wattana, Bangkok, Wattana",
            'city_id' => 1936,
            'lat_coordinate' => 13.699749,
            'lng_coordinate' => 100.603950,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJ69UzUM-e4jARwyvJBkeTbr0",
            'location' => "Amari Watergate Bangkok",
            'address' => "847 Phetchaburi Road",
            'city_id' => 1936,
            'lat_coordinate' => 13.751178,
            'lng_coordinate' => 100.540066,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJ-eIsmEme4jARJj2YJYuFOAA",
            'location' => "Wattana Panich",
            'address' => "336 Ekkamai Soi 18",
            'city_id' => 1936,
            'lat_coordinate' => 13.734441,
            'lng_coordinate' => 100.587628,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJodgF9j8dDTkRPbnohH9HcZo",
            'location' => "Bukhara, Diplomatic Enclave",
            'address' => "ITC Maurya, Sardar Patel Marg, Akhaura Block, Diplomatic Enclave, Chanakyapuri",
            'city_id' => 2544,
            'lat_coordinate' => 28.597701,
            'lng_coordinate' => 77.173865,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJkZedEMjiDDkRTRaqQAiFNi8",
            'location' => "Veda, Connaught Place",
            'address' => "H-27, Tropical Building, Block H, Connaught Place",
            'city_id' => 2544,
            'lat_coordinate' => 28.635205,
            'lng_coordinate' => 77.218013,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJ7TIykP7mazkRAh5DeEfUfyk",
            'location' => "Karims Restaurant",
            'address' => "Civil Lines, Ajmer, Rajasthan, India",
            'city_id' => 2544,
            'lat_coordinate' => 26.469655,
            'lng_coordinate' => 74.64380299999999,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJG11x-x5nHTERIDJ9p726taw",
            'location' => "Dharasom's Colonial House",
            'address' => "Lad-Krabang Soi 26",
            'city_id' => 1936,
            'lat_coordinate' => 13.720304,
            'lng_coordinate' => 100.735849,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJI4ffGo-Y4jARFo-OT7ccz24",
            'location' => "Anantara Riverside Bangkok Resort",
            'address' => "257/1-3 Charoennakorn Rd. Samrae, Thonburi",
            'city_id' => 1936,
            'lat_coordinate' => 13.704763,
            'lng_coordinate' => 100.491960,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJiX5NaHNLHDER9-GNuRPxfeA",
            'location' => "Khao Yai National Park",
            'address' => "Hin Tung, Mueang Nakhon Nayok District, Nakhon Nayok 26000",
            'city_id' => 1936,
            'lat_coordinate' => 14.4391748,
            'lng_coordinate' => 101.3724849,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJATr1n-Fx5kcRjQb6q6cdQDY",
            'location' => "Cathédrale Notre-Dame de Paris",
            'address' => "6 Parvis Notre-Dame - Pl. Jean-Paul II",
            'city_id' => 694,
            'lat_coordinate' => 48.853222,
            'lng_coordinate' => 2.349870,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('locations')->insert([
            'place_id' => "ChIJJwczql1x5kcRjjnV8q5DCnM",
            'location' => "Seine River",
            'address' => "Quai d'Anjou, Paris, France",
            'city_id' => 694,
            'lat_coordinate' => 48.8512336,
            'lng_coordinate' => 2.359642500000001,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
        
    }
}
