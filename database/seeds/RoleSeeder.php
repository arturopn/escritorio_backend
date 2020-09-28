<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('roles')->insert([
        'rolName' => 'Super Admin',

    ]);

    DB::table('roles')->insert([
      'rolName' => 'Abogado',

    ]);

    DB::table('roles')->insert([
      'rolName' => 'Admin Marketing',

    ]);
    DB::table('roles')->insert([
      'rolName' => 'Operador',

    ]);

    DB::table('roles')->insert([
      'rolName' => 'Usuario',

    ]);
    }
}
