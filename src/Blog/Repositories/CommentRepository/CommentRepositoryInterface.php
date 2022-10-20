<?php

namespace Granal1\Php2\Blog\Repositories\CommentRepository;

use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}