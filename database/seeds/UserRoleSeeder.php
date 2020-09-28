<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('user_roles')->insert([
      'userId' => '1',
      'rolId' => '1',
    ]);
    }
}
