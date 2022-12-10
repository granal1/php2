<?php

namespace Granal1\Php2\Http\Auth;

use Granal1\Php2\Http\Actions\ActionInterface;
use DateTimeImmutable;

use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;

use Granal1\Php2\Blog\Exceptions\AuthException;
use Psr\Log\LoggerInterface;
use Granal1\Php2\Http\Auth\TokenAuthenticationInterface;
use Granal1\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{
    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface $authTokensRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger
        ) {
    }

    public function handle(Request $request): Response
    {
        // Идентифицируем пользователя -
        // автора статьи
        try {
            $token = $this->authentication->token($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем токен с текущей датой окончания
        $authToken = new AuthToken(
            (string)$token,
            $user->uuid(),
            // Срок годности - сейчас
            new DateTimeImmutable()
        );

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);

        // Возвращаем токен
        return new SuccessfulResponse([
            'token disabled' => (string)$authToken->token()
        ]);
    }
}