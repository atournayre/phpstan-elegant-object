<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NoGettersAndSetters;

class AllowedMethodsClass
{
    public function getSomethingAllowed(): string
    {
        return 'allowed';
    }
}
