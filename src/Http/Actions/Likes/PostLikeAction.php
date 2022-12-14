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
use Granal1\Php2\Blog\Exceptions\AuthException;
use Psr\Log\LoggerInterface;
use Granal1\Php2\Http\Auth\TokenAuthenticationInterface;

class PostLikeAction implements ActionInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository,
        private PostRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication,
        // Внедряем контракт логгера
        private LoggerInterface $logger
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

        // Попытка найти статью в репозитории
        try {
            $post = $this->postRepository->get($postUuid);
        } catch (UserNotFoundException $e) {
            $this->logger->warning("Post not find: $postUuid.' '.$e->getMessage()");
            return new ErrorResponse($e->getMessage());
        }

        // Идентифицируем пользователя -
        // автора статьи
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
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

        // Логируем UUID новой статьи
        $this->logger->info("PostLike processed: $newPostLikeUuid"); 

        $postLikeCount = $this->postLikeRepository->getByPostUuid($postLike->getPost());
        return new SuccessfulResponse([
            'postLikeCount' => $postLikeCount,
        ]);
    }
}