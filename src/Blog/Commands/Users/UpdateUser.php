<?php

namespace Granal1\Php2\Blog\Commands\Users;

use Symfony\Component\Console\Command\Command;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Person\Name;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                //Имя опции
                'first-name',
                //Сокращенное имя
                'f',
                //Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                //Описание
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );

    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int
    {
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        //Выходим, если обе опции пусты
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        //Получаем UUID из аргумента
        $uuid = new UUID($input->getArgument('uuid'));

        //Получаем пользователя из репозитория
        $user = $this->usersRepository->get($uuid);

        //Создаем объект обновленного имени
        //если опция пуста, берем из репозитория
        $updatedName = new Name(
            empty($firstName)
                ? $user->name()->first() : $firstName,
            empty($lastName)
                ? $user->name()->last() : $lastName,
        );

        //Создаем новый объект пользователя
        $updatedUser = new User(
            $uuid,
            $updatedName,
            $user->username(),
            $user->getHashedPassword()
        );

        //Сохраняем обновленного пользователя
        $this->usersRepository->save($updatedUser);

        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;
    }
}