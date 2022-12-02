<?php

namespace Granal1\Php2\Http\Actions\Comments;

use Granal1\Php2\Http\Actions\ActionInterface;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;

use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Comment;

use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\InvalidArgumentException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;

use Granal1\Php2\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Psr\Log\LoggerInterface;


class CreateComment implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger
        ) {
    }

    public function handle(Request $request): Response
    {
        // Попытка создать UUID пользователя из данных запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Попытка создать UUID статьи из данных запроса
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Пытаемся найти пользователя в репозитории
        try {
            $author = $this->userRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            $this->logger->warning("Author not find: $authorUuid.' '.$e->getMessage()");
            return new ErrorResponse($e->getMessage());
        }

        // Попытка найти статью в репозитории
        try {
            $post = $this->postRepository->get($postUuid);
        } catch (UserNotFoundException $e) {
            $this->logger->warning("Post not find: $postUuid.' '.$e->getMessage()");
            return new ErrorResponse($e->getMessage());
        }

        // Генерация UUID для нового комментария
        // Попытка создать объект комментария
        // из данных запроса
        $newCommentUuid = UUID::random();
        try {
        $comment = new Comment(
            $newCommentUuid,
            $post,
            $author,
            $request->jsonBodyField('text'),
        );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новый комментарий в репозитории
        // Возвращаем успешный ответ,
        // содержащий UUID нового коментария
        $this->commentRepository->save($comment);

        // Логируем UUID новой статьи
        $this->logger->info("Comment created: $newCommentUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}