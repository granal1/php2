<?php

namespace Granal1\Php2\Blog\Repositories\PostLikeRepository;

use Granal1\Php2\Blog\Repositories\PostLikeRepository\PostLikeRepositoryInterface;
use Granal1\Php2\Blog\PostLike;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\UUID;
use PDO;


class SqlitePostLikeRepository implements PostLikeRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection) 
    {
        $this->connection = $connection;
    }

    public function save(PostLike $postLike): void
    {
        $likeExist = $this->isExist($postLike);
        if ($likeExist === false){
            $statement = $this->connection->prepare(
                'INSERT INTO post_likes (uuid, post_uuid, author_uuid)
                VALUES (:uuid, :post_uuid, :author_uuid)'
            );
    
            $statement->execute([
                ':uuid' => (string)$postLike->getUuid(),
                ':post_uuid' => (string)$postLike->getPost()->getUuid(),
                ':author_uuid' => (string)$postLike->getUser()->uuid()
            ]);
        }
        else {
            $statement = $this->connection->prepare(
                'DELETE FROM post_likes WHERE uuid = :uuid'
            );
            $statement->execute([
                ':uuid' => (string)$likeExist
            ]);
        }
    }

    public function getByPostUuid(Post $post): int
    {
        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM post_likes WHERE post_uuid = :post_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string)$post->getUuid()
        ]);

        $result = $statement->fetchColumn();
        return $result;
    }

    public function isExist(PostLike $postLike)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM post_likes WHERE post_uuid = :post_uuid
                                        AND author_uuid = :author_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string)$postLike->getPost()->getUuid(),
            ':author_uuid' => (string)$postLike->getUser()->uuid()
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return (false === $result) ? false : new UUID($result['uuid']);
    }
}