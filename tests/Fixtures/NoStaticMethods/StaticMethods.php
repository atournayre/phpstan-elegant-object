<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods;

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
