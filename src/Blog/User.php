<?php

namespace Granal1\Php2\Blog;

use Granal1\Php2\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;

    public function __construct(UUID $uuid, Name $name, string $username)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
    }

    public function __toString(): string
    {
        return "Пользователь $this->uuid с именем $this->name и логином $this->username" . PHP_EOL;
    }



    /**
     * Get the value of id
     */ 
    public function uuid(): UUID
    {
        return $this->uuid;
    }


    /**
     * Get the value of username
     */ 
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of login
     */ 
    public function username()
    {
        return $this->username;
    }

    /**
     * Set the value of login
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}