<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods;

interface AllowedInterface
{
    /**
     * @return string
     */
    public static function someStaticMethod();
}