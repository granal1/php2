<?php

namespace Granal1\Php2\Http\Auth;

use DateTimeImmutable;
use Granal1\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Blog\User;

use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\AuthException;
use Granal1\Php2\Blog\Exceptions\AuthTokenNotFoundException;
use Granal1\Php2\Http\Auth\AuthToken;


class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository,
        // Репозиторий пользователей
        private UserRepositoryInterface $usersRepository,
    ) {
    }

    public function user(Request $request): User
    {
        /*
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
        */
        $authToken = $this->token($request);

        // Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();

        // Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }

    public function token(Request $request): AuthToken
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }

        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }

        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
        else {
            return $authToken;
        }

    }


}