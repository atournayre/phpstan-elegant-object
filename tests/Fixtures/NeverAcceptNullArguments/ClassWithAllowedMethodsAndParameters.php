<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\NeverAcceptNullArguments;

class ClassWithAllowedMethodsAndParameters
{
    public function allowedMethod(?string $param1, $param2 = null): void
    {
        // Cette méthode est dans la liste des méthodes autorisées
    }

    public function anotherAllowedMethod(?int $someParam, ?string $anotherParam = null): void
    {
        // Cette méthode est aussi dans la liste des méthodes autorisées
    }

    public function normalMethod(string $param): void
    {
        // Méthode normale sans paramètres nullables
    }
}
