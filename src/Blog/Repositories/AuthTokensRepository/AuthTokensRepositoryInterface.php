<?php

namespace Granal1\Php2\Blog\Repositories\AuthTokensRepository;

use Granal1\Php2\Http\Auth\AuthToken;

interface AuthTokensRepositoryInterface
{
    // Метод сохранения токена
    public function save(AuthToken $authToken): void;
    
    // Метод получения токена
    public function get(string $token): AuthToken;
}