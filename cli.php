<?php

use Granal1\Php2\User\User;
use Granal1\Php2\Article\Article;
use Granal1\Php2\Comment\Comment;

$faker = Faker\Factory::create();


$user = new User (1, $faker->firstName, $faker->lastName);
$article = new Article (1, $user->id, $faker->text($maxNbChars = 20), $faker->text($maxNbChars = 200));
$comment = new Comment (1, $user->id, $article->id, $faker->text($maxNbChars = 50));

print_r($user);
print_r($article);
print_r($comment);

