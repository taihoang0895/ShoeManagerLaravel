<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Campaign;
use Faker\Generator as Faker;

$factory->define(\App\models\Discount::class, function (Faker $faker) {
    return [
        'code' => $faker->numerify('MKM-###'),
        'name' => $faker->name,
        'note' => $faker->text,
        'start_time' => now(),
        'end_time' => now(),

    ];
});
