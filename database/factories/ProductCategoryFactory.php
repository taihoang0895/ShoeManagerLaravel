<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\models\ProductCategory::class, function (Faker $faker) {
    return [
        'size' => $faker->randomElements($array=array(["36,37,38,39,40,41"])),
        'color' => $faker->randomElements($array=array(["XANH","ĐỎ","Xám"])),
    ];
});
