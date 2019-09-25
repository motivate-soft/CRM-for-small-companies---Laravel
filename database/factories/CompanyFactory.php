<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Company::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create(['role' => 'company'])->id;
        },
        'vat_number' => $faker->randomNumber(5),
        'address' => $faker->address,
        'country' => $faker->country,
        'signatory' => $faker->imageUrl,
    ];
});
