<?php

namespace Granal1\Php2\Blog\Repositories\UsersRepository;

use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->user[] = $user;
    }

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user){
            if($user->uuid === $uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }
    
}

