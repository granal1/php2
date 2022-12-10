<?php

namespace Granal1\Php2\Blog;

use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\User;

class PostLike
{
    private UUID $uuid;
    private Post $post;
    private User $user;

    public function __construct(UUID $uuid, Post $post, User $user)
    {
        $this->uuid = $uuid;
        $this->post = $post;
        $this->user = $user;
    }

    public function __toString()
    {
        return  'uuid=' . $this->uuid . 
                ', post=' . $this->post . 
                ', user=' . $this->user;
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
     * Get the value of post
     */ 
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set the value of post
     *
     * @return  self
     */ 
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}