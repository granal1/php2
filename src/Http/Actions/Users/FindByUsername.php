<?php

namespace Granal1\Php2\Http\Actions\Users;

use Granal1\Php2\Http\Actions\ActionInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Psr\Log\LoggerInterface;


class FindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger) // Добавили зависимость от логгера
    {
        //
    }

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            $this->logger->warning("User not find: $username.' '.$e->getMessage()");
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->username(),
            'name' => $user->name()->first() . ' ' . $user->name()->last(),
        ]);
    }

}