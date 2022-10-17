<?php

use Granal1\Php2\User\User;
use Granal1\Php2\Article\Article;
use Granal1\Php2\Comment\Comment;

$user = new User (1, 'Иван', 'Иванов');
$article = new Article (1, $user->id, '...Заголовок статьи...', '...Содержимое статьи...');
$comment = new Comment (1, $user->id, $article->id, '...Комментарий...');

print_r($user);
print_r($article);
print_r($comment);
