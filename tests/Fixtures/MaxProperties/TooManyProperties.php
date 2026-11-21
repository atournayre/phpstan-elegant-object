<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\MaxProperties;

final class TooManyProperties
{
    private string $a = '';
    private string $b = '';
    private string $c = '';

    public function __construct(private int $d, private int $e)
    {
    }
}
