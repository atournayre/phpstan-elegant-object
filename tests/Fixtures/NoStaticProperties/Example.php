<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticProperties;

final class Example
{
    private static int $counter = 0;
    public static ?Example $instance = null;
    private static $first, $second;
    private const LIMIT = 3;
    private int $ok = 1;

    public function remember(): int
    {
        static $calls = 0;

        return ++$calls;
    }

    public function read(): mixed
    {
        global $config;

        return $config;
    }

    public function superGlobal(): mixed
    {
        return $GLOBALS['key'];
    }

    public function fine(): int
    {
        return self::LIMIT + $this->ok;
    }
}
