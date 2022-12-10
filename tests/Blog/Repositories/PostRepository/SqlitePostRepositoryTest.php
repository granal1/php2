<?php

namespace Granal1\Php2\tests\Blog\Repositories\PostRepository;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Person\Name;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use Granal1\Php2\Blog\Exceptions\PostNotFoundException;
use Granal1\Php2\Blog\Post;




class SqlitePostRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $uuid = new UUID('123e4567-e89b-12d3-a456-426614104000');

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);        
        
        $repository = new SqlitePostRepository($connectionStub);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Cannot find post: $uuid");

        $repository->get($uuid);

    }


    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
        ->expects($this->once()) // Ожидаем, что будет вызван один раз
        ->method('execute')
        ->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614104000',
            ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':title' => 'Заголовок поста',
            ':text' => 'Текст поста'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostRepository($connectionStub);
        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614104000'),
                new User(
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
                    new Name('Ivan', 'Nikitin'),
                    'ivan123'
                ),
                'Заголовок поста',
                'Текст поста'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStubPost = $this->createStub(PDOStatement::class);
        //$statementStubUser = $this->createStub(PDOStatement::class);
        $statementStubPost
            ->method('fetch')
            ->willReturn([
                'uuid'          => '123e4567-e89b-12d3-a456-426614104000',
                'author_uuid'   => '123e4567-e89b-12d3-a456-426614174000',
                'first_name'    => 'Ivan',
                'last_name'     => 'Nikitin',
                'username'      => 'ivan123',
                'title'         => 'Заголовок поста',
                'text'          => 'Текст поста'
            ]);
        
        /*
        $statementStubUser
            ->method('fetch')
            ->willReturn([
                'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
                'username' => 'ivan123',
            ]);
        */
        $connectionStub
            ->method('prepare')
            ->willReturn(
                $statementStubPost,
        //        $statementStubUser
            );
        $postRepository = new SqlitePostRepository($connectionStub);
        $post = $postRepository->get(new UUID('123e4567-e89b-12d3-a456-426614104000'));
        $this->assertSame('123e4567-e89b-12d3-a456-426614104000', (string)$post->getUuid());



    }


}