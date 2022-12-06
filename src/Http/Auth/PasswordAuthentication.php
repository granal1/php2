<?php

namespace Granal1\Php2\Http\Auth;

use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\AuthException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Http\Request;


class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(private UserRepositoryInterface $usersRepository)
    {
        //
    }

    public function user(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль
        // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        // Пользователь аутентифицирован
        return $user;
    }
}