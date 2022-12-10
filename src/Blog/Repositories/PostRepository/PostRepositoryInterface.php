<?php

namespace Granal1\Php2\Blog\Repositories\PostRepository;

use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\UUID;

interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $post): Post;
    public function delete(Post $post): void;
}