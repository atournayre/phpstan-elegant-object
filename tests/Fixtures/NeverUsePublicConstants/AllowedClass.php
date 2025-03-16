<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverUsePublicConstants;

class AllowedClass
{
    public const ALLOWED_PUBLIC_CONSTANT = 'This class is in the allowed list';
}
