<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods;

class AllowedStaticMethod
{
    /**
     * @return string
     */
    public static function allowedStaticMethod()
    {
        return 'this is allowed';
    }
}
