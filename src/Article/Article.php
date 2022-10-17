<?php

namespace Granal1\Php2\Article;

use Granal1\Php2\User\User;

class Article
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

}

