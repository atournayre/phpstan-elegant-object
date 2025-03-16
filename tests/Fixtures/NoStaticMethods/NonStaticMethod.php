<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoStaticMethods;

final readonly class NonStaticMethod
{
    /**
     * @return string
     */
    public function normalMethod()
    {
        return 'this is fine';
    }
}
