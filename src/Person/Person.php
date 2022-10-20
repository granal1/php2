<?php

namespace Granal1\Php2\Person;

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

        public function __toString()
    {
        return 'id=' . $this->id . ', firstName=' . $this->firstName . ', lastName=' . $this->lastName;
    }

}
