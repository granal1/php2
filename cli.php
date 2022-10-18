<?php

require_once ('vendor/autoload.php');

use Granal1\Php2\User\User;
use Granal1\Php2\Post\Post;
use Granal1\Php2\Comment\Comment;

if(array_key_exists('1', $argv)){
    $faker = Faker\Factory::create();

    foreach ($argv as $value) {
        if ($value == 'user'){
            $user = new User(1, $faker->firstName, $faker->lastName);
            echo $user->firstName . ' ' . $user->firstName . PHP_EOL;
        }

        if ($value == 'post'){
            $post = new Post(1, 1, $faker->text($maxNbChars = 20), $faker->text($maxNbChars = 200));
            echo $post->title . ' >>> ' . $post->content . PHP_EOL;
        }

        if ($value == 'comment'){
            $comment = new Comment(1, 1, 1, $faker->text($maxNbChars = 50));
            echo $comment->comment . PHP_EOL;
        }
    }
}
