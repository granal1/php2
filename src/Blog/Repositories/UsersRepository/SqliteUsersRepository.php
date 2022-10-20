<?php

namespace Granal1\Php2\Blog\Repositories\UsersRepository;

use Granal1\Php2\Blog\User;
use Granal1\Php2\Person\Name;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection) 
    {
        $this->connection = $connection;
    
    }


    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
            VALUES (:uuid, :username, :first_name, :last_name)'
        );

        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => (string)$user->username(),
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last()
        ]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getUser($statement, $uuid);
        
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
          'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
          ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }


    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new UserNotFoundException(
            "Cannot find user: $username"
            );
        }

        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }

}