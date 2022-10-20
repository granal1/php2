<?php

namespace Granal1\Php2\Blog;

use Granal1\Php2\User\User;
use Granal1\Php2\User\Article;

class Comment
{
    private UUID $uuid;
    private UUID $postUuid;
    private UUID $authorUuid;
    private $text;

    public function __construct(UUID $uuid, UUID $postUuid, UUID $authorUuid, $text)
    {
        $this->uuid = $uuid;
        $this->postUuid = $postUuid;
        $this->authorUuid = $authorUuid;
        $this->text = $text;
    }

    public function __toString()
    {
        return  'uuid=' . $this->uuid . 
                ', postUuid=' . $this->postUuid . 
                ', authorUuid=' . $this->authorUuid . 
                ', text=' . $this->text;
    }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get the value of postUuid
     */ 
    public function getPostUuid()
    {
        return $this->postUuid;
    }

    /**
     * Set the value of postUuid
     *
     * @return  self
     */ 
    public function setPostUuid($postUuid)
    {
        $this->postUuid = $postUuid;

        return $this;
    }

    /**
     * Get the value of authorUuid
     */ 
    public function getAuthorUuid()
    {
        return $this->authorUuid;
    }

    /**
     * Set the value of authorUuid
     *
     * @return  self
     */ 
    public function setAuthorUuid($authorUuid)
    {
        $this->authorUuid = $authorUuid;

        return $this;
    }

    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the value of text
     *
     * @return  self
     */ 
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
}
