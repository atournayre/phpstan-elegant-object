<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods;

final readonly class TestClass
{
    /**
     * @return string
     */
    public static function someStaticMethod()
    {
        return 'this is allowed in tests';
    }
}
