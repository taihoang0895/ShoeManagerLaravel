<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\models\Product::class, function (Faker $faker) {
    return [
        'code' => $faker->numerify('MSP-###'),
        'name' => $faker->name,
        'price' => $faker->numberBetween($min=100, $max=400),
        'historical_cost' => $faker->numberBetween($min=10, $max=100),
        'created' => $faker->dateTime
    ];
});
