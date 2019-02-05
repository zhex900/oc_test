<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/



$factory->define(App\Person::class, function (Faker $faker) {
    $image_url ='https://randomuser.me/api/portraits/thumb/';
    $gender = $faker->randomElement(['men','women']);
    $gender_map = ['men'=>'male','women'=>'female'];
    return [
        'image' => $image_url.$gender.'/'.$faker->numberBetween(1,50).'.jpg',
        'first_name' => $faker->firstName($gender_map[$gender]),
        'last_name' => $faker->lastName.'-'.$faker->word,
        'address' => $faker->address,
        'mailing_address' => $faker->address,
        'phone' => $faker->numerify('04########')
    ];
});
