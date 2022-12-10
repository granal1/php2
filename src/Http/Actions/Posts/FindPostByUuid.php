<?php

namespace Granal1\Php2\Http\Actions\Posts;

use Granal1\Php2\Http\Actions\ActionInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\PostRepository\SqlitePostRepository;
use Granal1\Php2\Blog\Exceptions\HttpException;
use Granal1\Php2\Blog\Exceptions\PostNotFoundException;
use Granal1\Php2\Http\ErrorResponse;
use Granal1\Php2\Http\SuccessfulResponse;

use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;
use Granal1\Php2\Blog\UUID;



class FindPostByUuid implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postRepository->get($uuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Post_uuid = ' => $post->getUuid()->getUuidString(),
            'Author: ' =>[
                            'user_uuid = ' => $post-> getUser()->uuid()->getUuidString(),
                            'username = ' => $post-> getUser()->username(),
                            'name = ' => $post-> getUser()->name()->first() . ' ' . $post->getUser()->name()->last(),
                        ],
            'Post_title = ' => $post->getTitle(),
            'Post_text = ' => $post->getText(),
        ]);

    }
}