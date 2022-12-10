<?php

namespace Granal1\Php2\tests\Blog\Repositories\CommentRepository;

use PDO;
use PDOStatement;
use Granal1\Php2\Blog\Comment;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Person\Name;
use Granal1\Php2\Blog\Post;
use PHPUnit\Framework\TestCase;
use Granal1\Php2\Blog\Exceptions\CommentNotFoundException;
use Granal1\Php2\Blog\Repositories\CommentRepository\SqliteCommentRepository;


class SqliteCommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $uuid = new UUID('123e4567-e89b-12d3-a456-426614154000');

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);        
        
        $repository = new SqliteCommentRepository($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment: $uuid");

        $repository->get($uuid);

    }


    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
        ->expects($this->once()) // Ожидаем, что будет вызван один раз
        ->method('execute')
        ->with([
            ':uuid'         => '123e4567-e89b-12d3-a456-426614154000',
            ':post_uuid'    => '123e4567-e89b-12d3-a456-426614104000',
            ':author_uuid'  => '123e4567-e89b-12d3-a456-426614174000',
            ':text'         => 'Текст комментария к посту'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('Ivan', 'Nikitin'),
            'ivan123'
        );

        $post = new Post(
            new UUID('123e4567-e89b-12d3-a456-426614104000'),
            $user,
            'Заголовок поста',
            'Текст поста'
        );

        $comment = new Comment(
            new UUID('123e4567-e89b-12d3-a456-426614154000'),
            $post,
            $user,
            'Текст комментария к посту'
        );

        $repository = new SqliteCommentRepository($connectionStub);
        $repository->save($comment);
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStubComment = $this->createStub(PDOStatement::class);

        $statementStubComment
            ->method('fetch')
            ->willReturn([
                'uuid'          => '123e4567-e89b-12d3-a456-426614154000',
                'post_uuid'     => '123e4567-e89b-12d3-a456-426614104000',
                'author_uuid'   => '123e4567-e89b-12d3-a456-426614174000',
                'first_name'    => 'Ivan',
                'last_name'     => 'Nikitin',
                'username'      => 'ivan123',
                'title'         => 'Заголовок поста',
                'text'          => 'Текст комментария к посту'
            ]);
        
        $connectionStub
            ->method('prepare')
            ->willReturn(
                $statementStubComment
            );

        $commentRepository = new SqliteCommentRepository($connectionStub);
        $comment = $commentRepository->get(new UUID('123e4567-e89b-12d3-a456-426614154000'));
        $this->assertSame('123e4567-e89b-12d3-a456-426614154000', (string)$comment->getUuid());

    }


}