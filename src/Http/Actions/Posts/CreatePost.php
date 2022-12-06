<?php

namespace Granal1\Php2\Http\Actions\Posts;

use Granal1\Php2\Http\Actions\ActionInterface;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;

use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\User;

use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\InvalidArgumentException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\Exceptions\AuthException;

use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Http\Auth\TokenAuthenticationInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication,
        // Внедряем контракт логгера
        private LoggerInterface $logger
        ) 
    {
        //
    }

    public function handle(Request $request): Response
    {
        // Идентифицируем пользователя -
        // автора статьи
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем UUID для новой статьи
        // Пытаемся создать объект статьи
        // из данных запроса
        $newPostUuid = UUID::random();
        try {
        $post = new Post(
            $newPostUuid,
            $author,
            $request->jsonBodyField('title'),
            $request->jsonBodyField('text'),
        );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        $this->postRepository->save($post);

        // Логируем UUID новой статьи
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}