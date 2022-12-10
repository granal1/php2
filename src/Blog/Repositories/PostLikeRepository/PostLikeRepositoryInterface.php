<?php

namespace Granal1\Php2\Blog\Repositories\PostLikeRepository;

use Granal1\Php2\Blog\PostLike;
use Granal1\Php2\Blog\Post;

interface PostLikeRepositoryInterface
{
    public function save(PostLike $postLike): void;
    public function getByPostUuid(Post $post): int;
}