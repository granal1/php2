<?php

namespace App\Community_Blog\User;

//use App\Community_Blog\User\User_Post;
use App\Person\Person;

class User_Post
{
    public function __construct(private Person $author, private string $text)
    {

    }

    public function __toString()
    {
        return $this->author . ' пишет ' . $this->text;
    }
}

