<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Workcenter::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'country' => $faker->country,
        'state' => $faker->state,
        'city' => $faker->city,
        'zip_code' => $faker->postcode,
        'address' => $faker->address,
        'company_id' => function () {
            return App\Models\Company::inRandomOrder()->first()->id;
        },
    ];
});
