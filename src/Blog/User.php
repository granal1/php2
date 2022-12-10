<?php

namespace Granal1\Php2\Blog;

use Granal1\Php2\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $hashedPassword;

    public function __construct(UUID $uuid, Name $name, string $username, string $hashedPassword)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
    }

    
    // Функция для вычисления хеша
    private static function hash(string $password, UUID $uuid): string
    {
        /* TODO опробовать вариант:
        тут можнодобавить торможение определения хеша
        $hash = hash('sha256', $uuid . $password);
        for i = ($i = 1; $i <= 65535; $i++){
            $hash = hash('sha256', $hash . $uuid . $password);
        }
        */
        return hash('sha256', $uuid . $password);
    }

    // Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    // Функция для создания нового пользователя
    public static function createFrom(
        Name $name,
        string $username,
        string $password
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid)
        );
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

    /**
     * Get the value of hashedPassword
     */ 
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }
    
    /**
     * Set the value of hashedPassword
     *
     * @return  self
     */ 
    public function setHashedPassword($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;

        return $this;
    }
    
    public function __toString(): string
    {
        return "Пользователь $this->uuid с именем $this->name и логином $this->username" . PHP_EOL;
    }
}