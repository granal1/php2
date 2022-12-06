<?php

namespace Granal1\Php2\Blog\Repositories\AuthTokensRepository;

use Granal1\Php2\Http\Auth\AuthToken;
use DateTimeImmutable;
use DateTimeInterface;
use PDO;
use PDOException;
use Granal1\Php2\Blog\UUID;
use Exception;
use Granal1\Php2\Blog\Exceptions\AuthTokensRepositoryException;
use Granal1\Php2\Blog\Exceptions\AuthTokenNotFoundException;


class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                user_uuid,
                expires_on
            ) VALUES (
                :token,
                :user_uuid,
                :expires_on
            )
            ON CONFLICT (token) DO UPDATE SET
                expires_on = :expires_on
SQL;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => (string)$authToken->token(),
                ':user_uuid' => (string)$authToken->userUuid(),
                ':expires_on' => $authToken->expiresOn()
                                ->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }
}