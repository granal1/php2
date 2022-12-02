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
use Psr\Log\LoggerInterface;


class DeletePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger) // Добавили зависимость от логгера
    {
        //
    }

    public function handle(Request $request): Response
    {
        // Пытаемся создать UUID статьи из данных запроса
        try {
            $postUuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти статью в репозитории
        try {
            $post = $this->postRepository->get($postUuid);
        } catch (UserNotFoundException $e) {
            $this->logger->warning("Post not find: $postUuid.' '.$e->getMessage()");
            return new ErrorResponse($e->getMessage());
        }

        // Удаляем статью из репозитория
        $this->postRepository->delete($post);

        //Можно реализовать удаление всех комментариев к удаленному посту
        //Для этого целесообразно создать Action DeleteComment
        //А можно и не удалять. Комментарии иногда имеют собственную ценность

        $this->logger->info("Post deleted: $request->query('uuid')");

        return new SuccessfulResponse([
            'deleted' => $request->query('uuid'),
        ]);
    }

}