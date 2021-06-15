<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->insert([
      'name' => 'Arturo',
      'lastName' => 'Perez',
      'password' => bcrypt('1q2w3e4r'),
      'email' => 'arturo.perez87@hotmail.com',
      'cellphone' => '8119097089',
      'created_at' => now(),
      'updated_at' => now(),
      'photo' => 'https://cbsnews1.cbsistatic.com/hub/i/2016/09/29/d1a671d9-556e-468d-8639-159e2842f15b/logan-new-hamshire-cat-2016-09-29.jpg',
      'gender' => 1,
      'age' => 26,
      'status' => 'Active',
      'isLogged' => true,
      'coupon_code' => ''
    
    ]);
    }
}
