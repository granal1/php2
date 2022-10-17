<?php

namespace Granal1\Php2\Comment;

use Granal1\Php2\User\User;
use Granal1\Php2\User\Article;

class Comment
{
    public $id;
    public $authorId;
    public $articleId;
    public $comment;

    public function __construct($id, $authorId, $articleId, $comment)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->articleId = $articleId;
        $this->comment = $comment;
    }

    public function __toString()
    {
        return 'id=' . $this->id . ', authorId=' . $this->authorId . ', articleId=' . $this->articleId . ', comment=' . $this->comment;
    }

}
