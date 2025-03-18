<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

class ClassWithNullDefaultArguments
{
    public function methodWithNullDefault(string $param = null): void
    {
        // Cette méthode a un paramètre avec null comme valeur par défaut
    }

    public function methodWithMultipleParams(string $normalParam, $paramWithNullDefault = null): void
    {
        // Cette méthode a un paramètre normal et un paramètre avec null par défaut
    }

    public function methodWithoutNullDefaults(string $param1, int $param2 = 0): void
    {
        // Cette méthode n'a pas de paramètres avec null par défaut
    }
}
