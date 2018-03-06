<?php

use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(App\Mood::class, function (Faker $faker) {
    return [
        'id' => Uuid::generate(),
        'message' => $faker->text(200),
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
