<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\DontUseStaticMethods;

class StaticMethods
{
    /**
     * @return string
     */
    public static function notAllowedStaticMethod()
    {
        return 'this should trigger an error';
    }
}
