<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Granal1\Php2\Blog\Commands\CreateUserCommand;
use Granal1\Php2\Blog\Commands\Arguments;
use Granal1\Php2\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Granal1\Php2\Blog\Commands\Users\CreateUser;
use Granal1\Php2\Blog\Commands\Users\UpdateUser;
use Granal1\Php2\Blog\Commands\Posts\DeletePost;
use Granal1\Php2\Blog\Commands\FakeData\PopulateDB;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

// Запускаем приложение
$application->run();

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру
    // с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}

//include __DIR__ . "/vendor/autoload.php";

//$connection = new PDO ('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
//$usersRepository = new SqliteUsersRepository($connection); // Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository
//$command = new CreateUserCommand($usersRepository);

//Добавляем в репозиторий несколько пользователей
//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), "admin"));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), "user"));
//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), "admin"));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), "user"));
//echo $usersRepository->getByUsername("admin");

/*
try{
    $command->handle(Arguments::fromArgv($argv));
}catch (Exception $e){
    echo "{$e->getMessage()}\n";
}
*/
// проверка записи
/*
$postRepository = new SqlitePostRepository($connection);
$postRepository->save(  new Post(UUID::random(), 
                        new UUID("5fd5b1d4-8b2f-4c96-a82d-469c96aa38ab"), 
                        "Заголовок статьи автора paul155",
                        "Текст статьи автора paul155"));
*/

/*
$commentRepository = new SqliteCommentRepository($connection);
$commentRepository->save(   new Comment(UUID::random(), 
                            new UUID("7697cfc7-1bee-4218-984a-58d0a618d039"), 
                            new UUID("5fd5b1d4-8b2f-4c96-a82d-469c96aa38ab"), 
                            "Комментарий 2 к посту автора paul155"));
*/
//$postRepository = new SqlitePostRepository($connection);
//echo $postRepository->get(new UUID("7697cfc7-1bee-4218-984a-58d0a618d039"));