<?php

use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
      'name' => 'Poliza de servicios legales',
      'description' => 'Apartir de 10 mil pesos, 4 horas como minimo.',
    'imagename' => 'https://cbsnews1.cbsistatic.com/hub/i/2016/09/29/d1a671d9-556e-468d-8639-159e2842f15b/logan-new-hamshire-cat-2016-09-29.jpg',
    'price' => '10000',
      'created_at' => now(),
      'updated_at' => now(), 
    ]);
    }
}
