<?php

namespace Granal1\Php2\Http\Auth;

use Granal1\Php2\Http\Request;
use Granal1\Php2\Blog\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}