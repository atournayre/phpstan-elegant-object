<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

class ClassWithAllowedNullableArguments
{
    public function someMethod(?string $allowedParameter, ?int $anotherAllowedParameter): void
    {
        // Cette méthode a des paramètres nullables, mais ils sont dans la liste des paramètres autorisés
    }

    public function anotherMethod(string $normal, $allowedParameter = null): void
    {
        // Cette méthode a un paramètre avec null par défaut, mais il est dans la liste des paramètres autorisés
    }
}
