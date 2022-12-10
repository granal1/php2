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


use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;




class CreatePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        ) {
    }

    public function handle(Request $request): Response
    {
        // Пытаемся создать UUID пользователя из данных запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя в репозитории
        try {
            $author = $this->userRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
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
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }

}