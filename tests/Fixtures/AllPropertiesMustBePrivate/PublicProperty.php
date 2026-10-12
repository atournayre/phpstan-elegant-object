<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\AllPropertiesMustBePrivate;

final class PublicProperty
{
    // This should trigger the rule - property is not private
    public string $name = '';
}
