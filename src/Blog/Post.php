<?php

namespace Granal1\Php2\Blog;

class Post
{
    private UUID $uuid;
    private UUID $authorUuid;
    private $title;
    private $text;

    public function __construct(UUID $uuid, UUID $authorUuid, $title, $text)
    {
        $this->uuid = $uuid;
        $this->authorUuid = $authorUuid;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString()
    {
        return 'uuid=' . $this->uuid . ', 
                authorUuid=' . $this->authorUuid . ', 
                title=' . $this->title . ', 
                content=' . $this->text;
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
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

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

