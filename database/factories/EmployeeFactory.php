<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Employee::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create(['role' => 'employee'])->id;
        },
        'company_id' => function () {
            return App\Models\Company::inRandomOrder()->first()->id;
        },
        'employee_id' => $faker->randomNumber(5),
        'photo' => $faker->imageUrl,
        'nif' => $faker->randomNumber(5),
        'affiliation' => $faker->randomNumber(5),
    ];
});