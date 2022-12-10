<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\SuccessfulResponse;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\Actions\Users\FindByUsername;
use Granal1\Php2\Http\Actions\Posts\FindPostByUuid;
use Granal1\Php2\Http\Actions\Posts\CreatePost;
use Granal1\Php2\Http\Actions\Posts\DeletePost;
use Granal1\Php2\Http\Actions\Comments\CreateComment;
use Granal1\Php2\Http\Actions\Likes\PostLikeAction;

use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use Granal1\Php2\Blog\Repositories\CommentRepository\SqliteCommentRepository;
use Granal1\Php2\Blog\Repositories\PostLikeRepository\SqlitePostLikeRepository;
use Granal1\Php2\Blog\UUID;

use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\AppException;

use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require dirname(__DIR__, 1) . '/bootstrap.php';

//require_once (__DIR__.'/../vendor/autoload.php');

$request = new Request(
    $_GET, 
    $_SERVER,
    file_get_contents('php://input'),
);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод

    $method = $request->method();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindPostByUuid::class
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/postLike' => PostLikeAction::class,
        '/posts/comment' => CreateComment::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];


/*
$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
                            new SqliteUsersRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),

        '/posts/show' => new FindPostByUuid(
                            new SqlitePostRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
                            new SqlitePostRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            ),
                            new SqliteUsersRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),

        '/posts/postLike' => new PostLikeAction(
                            new SqlitePostLikeRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            ),
                            new SqlitePostRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            ),
                            new SqliteUsersRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),

        '/posts/comment' => new CreateComment(
                            new SqliteCommentRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            ),
                            new SqlitePostRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            ),
                            new SqliteUsersRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),
    ],
    'DELETE' => [
        '/posts' => new DeletePost(
                            new SqlitePostRepository(
                                new PDO('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite')
                            )
                        ),
    ],

];
*/


if (!array_key_exists($method, $routes)) {
    $message = "Method for route not found: $method";
    // Логируем сообщение с уровнем NOTICE
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    $message = "Path for route not found: $path";
    // Логируем сообщение с уровнем NOTICE
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

// С помощью контейнера
// создаём объект нужного действия
try {
        $action = $container->get($actionClassName);
        $response = $action->handle($request);
    } catch (Exception $e) {
        // Логируем сообщение с уровнем ERROR
        $logger->error($e->getMessage(), ['exception' => $e]);
        // Больше не отправляем пользователю
        // конкретное сообщение об ошибке,
        // а только логируем его
        (new ErrorResponse)->send();
        return;
    }

$response->send();


//Черновики и заметки:

//GET http://127.0.0.1:8000/users/show?username=ivan

/*
$request = new Request($_GET, $_SERVER);
$path = $request->path();
echo $path . PHP_EOL;
echo "параметр=". $request->query('some_parameter'). PHP_EOL;

// Создаём объект ответа
$response = new SuccessfulResponse([
    'message' => 'Hello from PHP',
]);

// Отправляем ответ
$response->send();
//$postRepository = new SqlitePostRepository($connection);
//echo $postRepository->get(new UUID("7697cfc7-1bee-4218-984a-58d0a618d039"));
$connection = new PDO ('sqlite:' . dirname(__DIR__, 1) . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);

$request = new Request($_GET, $_SERVER);
$path = $request->path();

$action = new FindByUsername($usersRepository);

$response = $action->handle($request);

$response->send();

{
"author_uuid": "fba326dd-7dd5-4cfb-8c1a-3d6390fa15b3",
"text": "Текст статьи автора filin98 (Oleg Pushin)",
"title": "Заголовок статьи автора filin98 (Oleg Pushin)"
}

*/
