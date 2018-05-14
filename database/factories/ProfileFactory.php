<?php

use Faker\Generator as Faker;

$factory->define(App\Profile::class, function (Faker $faker) {
    static $user_id;
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'nickname' => $faker->name,
        'gender' => $faker->randomElement(array('male','female')),
        'age' => $faker->numberBetween(18, 66),
        'bio' => $faker->paragraph,
        // profile_picture
        // cover_picture
        'user_id' => $user_id ?: function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
