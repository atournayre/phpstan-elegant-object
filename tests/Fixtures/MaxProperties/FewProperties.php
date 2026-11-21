<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\MaxProperties;

final class FewProperties
{
    public const LIMIT = 10;

    private static int $s1 = 0;
    private static int $s2 = 0;
    private static int $s3 = 0;

    private string $a = '';
    private string $b = '';

    public function __construct(private int $c, private int $d, int $notPromoted = 0)
    {
    }
}
