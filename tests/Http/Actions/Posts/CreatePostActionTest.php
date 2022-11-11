<?php

namespace Granal1\Php2\test\Http\Actions\Posts;

use PHPUnit\Framework\TestCase;
use Granal1\Php2\Http\Actions\Posts\CreatePost;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\Exceptions\PostNotFoundException;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Person\Name;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;


class CreatePostActionTest extends TestCase
{
    // Тест запускается в отдельном процессе
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    // Тест возвращает ошибку, если запрос содержит UUID в неверном формате
    public function testItReturnsErrorIfUuidWrongFormat(): void
    {
        $request = new Request([], [], '{"author_uuid": "wrong-uuid"}');
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePost($postRepository, $userRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: wrong-uuid"}');
        $response->send();
    }

    // Тест запускается в отдельном процессе
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    // Тест возвращает ошибку, если пользователь не найден по этому UUID;
    public function testItReturnsErrorIfUserByUuidNotFound(): void
    {
        $request = new Request([], [], '{"author_uuid": "7697cfc7-1bee-4218-984a-58d0a618d029"}');
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePost($postRepository, $userRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot find user"}');
        $response->send();
    }

    // Тест запускается в отдельном процессе
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    // Тест возвращает ошибку, если запрос не содержит всех данных, необходимых для создания статьи.
    public function testItReturnsErrorIfNotEnoughDataForCreate(): void
    {
        $request = new Request([], [], 
        '{"author_uuid": "7697cfc7-1bee-4218-984a-58d0a618d030",
        "text": "Текст статьи автора admin (Ivan Nikitin)"}');
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePost($postRepository, $userRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: title"}');
        $response->send();
    }

    // Тест запускается в отдельном процессе
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    // Тест возвращает успешный ответ
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], 
        '{"author_uuid": "7697cfc7-1bee-4218-984a-58d0a618d030",
        "title": "Заголовок статьи автора admin (Ivan Nikitin)",
        "text": "Текст статьи автора admin (Ivan Nikitin)"}');
        $postRepository = $this->postRepository([]);
        $userRepository = $this->userRepository([]);
        $action = new CreatePost($postRepository, $userRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputRegex(
            '{"success":true,"data":{"uuid":"[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"}}'
        );
        $response->send();
    }

    // Функция, создающая стаб репозитория статей,
    // принимает массив "существующих" статей
    private function postRepository(array $post): PostRepositoryInterface
    {
        // В конструктор анонимного класса передаём массив пользователей
        return new class($post) implements PostRepositoryInterface {

            public function __construct(
                private array $post
            ) 
            {
            }

            public function save(Post $post): void
            {
            }

            public function get(UUID $uuid): Post
            {
                if ($uuid->getUuidString() != '7697cfc7-1bee-4218-984a-58d0a618d039'){
                    throw new PostNotFoundException(
                        "Cannot find post: $uuid \n"
                        );
                }
                else{
                    return new Post(
                        new UUID ('7697cfc7-1bee-4218-984a-58d0a618d039'),
                        new User(
                            new UUID ('7697cfc7-1bee-4218-984a-58d0a618d030'),
                            new Name('Ivan', 'Nikitin'),
                            'ivan',
                        ),
                        'Заголовок статьи',
                        'Текст статьи'
                    );
                }
            }

            public function delete(Post $post): void
            {
            }
            
        };
    }

    // Функция, создающая стаб репозитория пользователей,
    // принимает массив "существующих" пользователей
    private function userRepository(array $users): UserRepositoryInterface
    {
        // В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UserRepositoryInterface {
            public function __construct(
                private array $users
            ) 
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                if((string)$uuid != '7697cfc7-1bee-4218-984a-58d0a618d030'){
                    throw new UserNotFoundException("Cannot find user");
                }
                else{
                    return new User(
                        new UUID ('7697cfc7-1bee-4218-984a-58d0a618d030'),
                        new Name('Ivan', 'Nikitin'),
                        'ivan',
                    );
                }
            }

            public function getByUsername(string $username): User
            {
                if ($username == 'ivan'){
                    return new User(
                        new UUID ('7697cfc7-1bee-4218-984a-58d0a618d030'),
                        new Name('Ivan', 'Nikitin'),
                        'ivan',
                    );
                }
            }
        };
    }
}