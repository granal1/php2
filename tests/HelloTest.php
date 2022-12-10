<?php

namespace Granal1\Php2\test;

use PHPUnit\Framework\TestCase;

final class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
        // Проверяем, что true – это true
        $this->assertTrue(true);
    }
}