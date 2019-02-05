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
    $gender = $faker->randomElement(['men','women']);
    $gender_map = ['men'=>'male','women'=>'female'];
    return [
        'image' => userImageGenerator($gender,10,1), //$image_url.$gender.'/'.$faker->numberBetween(1,99).'.jpg',
        'first_name' => $faker->firstName($gender_map[$gender]),
        'last_name' => $faker->lastName.'-'.$faker->word,
        'address' => $faker->address,
        'mailing_address' => $faker->address,
        'phone' => $faker->numerify('04########')
    ];
});

function userImageGenerator( $gender, $max_try, $i, $default_image='https://image.flaticon.com/icons/svg/147/147144.svg' ){
    $image_url ='https://randomuser.me/api/portraits/thumb/';
    $url =  $image_url.$gender.'/'.rand(1,99).'.jpg';
    if (@getimagesize($url)) {
        echo 'generate user image: '. $url.PHP_EOL;
        return $url;
    }elseif( $i <= $max_try) {
        echo 'image does not exist, try again: '. $url.PHP_EOL;
        return userImageGenerator($gender, $max_try, ++$i, $default_image);
    }else {
        return $default_image;
    }
}