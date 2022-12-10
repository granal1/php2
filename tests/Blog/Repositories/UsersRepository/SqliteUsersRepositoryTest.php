<?php

namespace Granal1\Php2\tests\Blog\Repositories\UsersRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Granal1\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Granal1\Php2\Blog\Exceptions\UserNotFoundException;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Person\Name;



class SqliteUsersRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);        
        
        $repository = new SqliteUsersRepository($connectionStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Cannot find user: Ivan");

        $repository->getByUsername('Ivan');

    }


    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
        ->expects($this->once()) // Ожидаем, что будет вызван один раз
        ->method('execute')
        ->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':first_name' => 'Ivan',
            ':last_name' => 'Nikitin',
            ':username' => 'ivan123',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteUsersRepository($connectionStub);
        $repository->save(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123'
            )
        );
    }


}