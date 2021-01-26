<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UsersLawyerChat;
use Faker\Generator as Faker;

$factory->define(UsersLawyerChat::class, function (Faker $faker) {

    return [
        'user_id' => $faker->randomDigitNotNull,
        'lawyer_id' => $faker->word,
        'firebase_chatId' => $faker->word,
        'firebase_userId' => $faker->word,
        'firebase_lawyerId' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
