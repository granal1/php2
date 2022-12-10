<?php

use Granal1\Php2\Blog\Container\DIContainer;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Granal1\Php2\Blog\Repositories\PostLikeRepository\PostLikeRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostLikeRepository\SqlitePostLikeRepository;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();

// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ .  '/' . $_SERVER['SQLITE_DB_PATH'])
);

// 2. репозиторий статей
$container->bind(
    PostRepositoryInterface::class,
    SqlitePostRepository::class
);

// 3. репозиторий пользователей
$container->bind(
    UserRepositoryInterface::class,
    SqliteUsersRepository::class
);

// 4. репозиторий лайков к статьям
$container->bind(
    PostLikeRepositoryInterface::class,
    SqlitePostLikeRepository::class
);


// .. ассоциируем объект логгера из библиотеки monolog
$logger = (new Logger('blog')); // blog – это (произвольное) имя логгера

// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger
    ->pushHandler(
        new StreamHandler("php://stdout")
    );
}

$container->bind(
    LoggerInterface::class,
    $logger
);

// Возвращаем объект контейнера
return $container;