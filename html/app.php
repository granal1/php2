<?php

//use App\Blog\Post;                //старый класс Post
use App\Person\Person;
use App\Person\Name;
use App\Community_Blog\User\User_Post; // имеет черту в названии папки, которая должна остаться и черту в имени класса, которую надо преобразовать в разделитель папок


$post = new User_Post(
    new Person(
        new Name ('Иван', 'Никитин'),
        new DateTimeImmutable()
    ),
    'Всем привет!'
);

print $post . PHP_EOL;
