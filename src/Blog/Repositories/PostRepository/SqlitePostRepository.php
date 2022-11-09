<?php

namespace Granal1\Php2\Blog\Repositories\PostRepository;

use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Exceptions\PostNotFoundException;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use PDO;
use PDOStatement;

class SqlitePostRepository implements PostRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection) 
    {
        $this->connection = $connection;
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new PostNotFoundException(
            "Cannot find post: $uuid \n"
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }

    public function delete(Post $post): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$post->getUuid()
        ]);
    }
}