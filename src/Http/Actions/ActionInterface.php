<?php

namespace Granal1\Php2\Http\Actions;
use Granal1\Php2\Http\Request;
use Granal1\Php2\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}