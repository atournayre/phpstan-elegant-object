<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverReturnNull;

class AllowedMethodClass
{
    // This should not trigger the rule due to allowed method name
    public function allowedMethod(): ?string
    {
        return null;
    }
}
