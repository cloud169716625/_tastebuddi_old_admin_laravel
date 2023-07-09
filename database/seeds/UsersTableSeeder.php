<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name'        => 'Admin',
            'last_name'        => 'Admin',
            'email'             => 'admin@admin.com',
            'password'          => bcrypt('123123123'),
            'mobile_number'         => '639999999999'
        ]);

        DB::table('users')->insert([
            'first_name'        => 'Gwest',
            'last_name'        => 'Gwest',
            'email'             => 'guest@guest.com',
            'password'          => bcrypt('123123123'),
            'mobile_number'         => '639999999990'
        ]);
    }
}
