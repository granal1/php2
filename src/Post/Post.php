<?php

namespace Granal1\Php2\Post;

use Granal1\Php2\User\User;

class Post
{
    public $id;
    public $authorId;
    public $title;
    public $content;

    public function __construct($id, $authorId, $title, $content)
    {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->content = $content;
    }

    public function __toString()
    {
        return 'id=' . $this->id . ', authorId=' . $this->authorId . ', title=' . $this->title . ', content=' . $this->content;
    }

}

