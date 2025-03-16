<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods;

final readonly class InMemoryClass
{
    /**
     * @return string
     */
    public static function someStaticMethod()
    {
        return 'this is allowed in InMemory classes';
    }
}
