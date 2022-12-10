<?php

use Granal1\Php2\Blog\Container\DIContainer;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Granal1\Php2\Blog\Repositories\PostLikeRepository\PostLikeRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostLikeRepository\SqlitePostLikeRepository;
use Granal1\Php2\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Granal1\Php2\Blog\Repositories\CommentRepository\SqliteCommentRepository;
use Granal1\Php2\Http\Auth\AuthenticationInterface;
use Granal1\Php2\Http\Auth\JsonBodyUuidIdentification;
use Granal1\Php2\Http\Auth\PasswordAuthentication;
use Granal1\Php2\Http\Auth\PasswordAuthenticationInterface;
use Granal1\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Granal1\Php2\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Granal1\Php2\Http\Auth\TokenAuthenticationInterface;
use Granal1\Php2\Http\Auth\BearerTokenAuthentication;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;
use Faker\Generator;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;

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

//5. репозиторий комментариев к статьям
$container->bind(
    CommentRepositoryInterface::class,
    SqliteCommentRepository::class
);



//Идентификация
$container->bind(
    AuthenticationInterface::class,
    JsonBodyUuidIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
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

// Создаём объект генератора тестовых данных
$faker = new \Faker\Generator();

// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    \Faker\Generator::class,
    $faker
);

// Возвращаем объект контейнера
return $container;