<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods;

final readonly class Implementation implements AllowedInterface
{
    /**
     * @return string
     */
    public static function someStaticMethod()
    {
        return 'this is allowed because it implements an allowed interface';
    }
}
