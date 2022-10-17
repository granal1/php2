<?php

namespace Granal1\Php2\User;

class User
{
    public $id;
    public $firstName;
    public $lastName;

    public function __construct($id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

}
