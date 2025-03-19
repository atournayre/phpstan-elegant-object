<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

use Assert\Assertion;

class ClassWithAssertionLibrary
{
    private string $email;
    private array $permissions;

    public function __construct(string $email, array $permissions)
    {
        $this->email = $email;
        $this->permissions = $permissions;

        Assertion::email($email, 'Invalid email address');
        Assertion::notEmpty($permissions, 'Permissions cannot be empty');
    }
}
