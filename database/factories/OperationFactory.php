<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Operation;
use App\Models\Category;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Operation::class, function (Faker $faker) {
    $userId = User::all()->random()->id;
    $catId = Category::whereNull('user_id')->get()->random()->id;
    $catName = Category::find($catId)->name;
    $comment = implode(' ', $faker->words());
    $tags = '#' . implode(' #', $faker->words());
    return [
        'date' => $faker->date(),
        'summ' => $faker->numberBetween(-10000,10000),
        'comment' => $comment,
        'search' => "{$catName}: {$comment} {$tags}",
        'category_id' => $catId,
        'user_id' => $userId,
        'tags' => $tags,
    ];
});
