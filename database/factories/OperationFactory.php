<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Operation;
use App\Models\Category;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Operation::class, function (Faker $faker) {
    $userId = User::all()->random()->id;
    $catId = Category::whereNull('user_id')->get()->random()->id;
    $catName = Category::find($catId)->search;
    $comment = implode(' ', $faker->words());
    return [
        'date' => $faker->date(),
        'summ' => $faker->numberBetween(100,10000),
        'comment' => $comment,
        'search' => "{$catName}: {$comment}",
        'category_id' => $catId,
        'user_id' => $userId,
    ];
});
