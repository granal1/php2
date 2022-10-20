<?php

namespace Granal1\Php2\Blog\Repositories\UsersRepository;

use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}