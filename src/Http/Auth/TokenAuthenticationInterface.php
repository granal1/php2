<?php

namespace Granal1\Php2\Http\Auth;

use Granal1\Php2\Http\Request;

interface TokenAuthenticationInterface extends AuthenticationInterface
{
    public function token(Request $request): AuthToken;
}