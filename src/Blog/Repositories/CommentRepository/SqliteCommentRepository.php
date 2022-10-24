<?php

namespace Granal1\Php2\Blog\Repositories\CommentRepository;

use Granal1\Php2\Blog\Comment;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\Exceptions\CommentNotFoundException;
use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use PDO;
use PDOStatement;

class SqliteCommentRepository implements CommentRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection) 
    {
        $this->connection = $connection;
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid' => (string)$comment->getPost()->getUuid(),
            ':author_uuid' => (string)$comment->getUser()->uuid(),
            ':text' => $comment->getText()
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getComment($statement, $uuid);
    }

    private function getComment(PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new CommentNotFoundException(
            "Cannot find comment: $uuid"
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        $postRepository = new SqlitePostRepository($this->connection);
        $post = $postRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }

}