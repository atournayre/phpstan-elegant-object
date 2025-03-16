<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods;

interface AllowedInterface
{
    /**
     * @return string
     */
    public static function someStaticMethod();
}