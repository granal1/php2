<?php

namespace Granal1\Php2\Blog\Commands;

use Granal1\Php2\Person\Name;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Exceptions\CommandException;
use Psr\Log\LoggerInterface;

//cli.php username=ivan_2 first_name=Ivan last_name=Nikitin

class CreateUserCommand
{
    public function __construct(private UserRepositoryInterface $usersRepository,
                                private LoggerInterface $logger) // Добавили зависимость от логгера
    {

    }

    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            
            // Вместо выбрасывания исключения просто выходим из функции
            return;
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            new Name($arguments->get('first_name'), 
                     $arguments->get('last_name')),
            $username
        ));

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $uuid");
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}