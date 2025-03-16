<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUseGettersAndSetters;

class AllowedMethodsClass
{
    public function getSomethingAllowed(): string
    {
        return 'allowed';
    }
}
