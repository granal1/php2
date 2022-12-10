<?php

namespace Granal1\Php2\Http\Actions\Likes;

use Granal1\Php2\Http\Actions\ActionInterface;

use Granal1\Php2\Blog\Repositories\PostLikeRepository\PostLikeRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;

use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;

use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\PostLike;

use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\InvalidArgumentException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;

class PostLikeAction implements ActionInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        ) {
    }

    public function handle(Request $request): Response
    {
        // Попытка создать UUID статьи из данных запроса
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        // Попытка создать UUID пользователя из данных запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Попытка найти статью в репозитории
        try {
            $post = $this->postRepository->get($postUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя в репозитории
        try {
            $author = $this->userRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        // Генерация UUID для нового лайка
        // Попытка создать объект лайка
        // из данных запроса
        $newPostLikeUuid = UUID::random();
        try {
        $postLike = new PostLike(
            $newPostLikeUuid,
            $post,
            $author,
        );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новый лайк статьи в репозитории
        // или удалаем лайк, если такой уже был
        // Возвращаем успешный ответ,
        // содержащий итоговое количество лайков в статье
        $this->postLikeRepository->save($postLike);
        $postLikeCount = $this->postLikeRepository->getByPostUuid($postLike->getPost());
        return new SuccessfulResponse([
            'postLikeCount' => $postLikeCount,
        ]);
    }
}