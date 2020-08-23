<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\models\CampaignName::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'created' => now(),
    ];
});
