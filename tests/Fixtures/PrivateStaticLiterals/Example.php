<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\PrivateStaticLiterals;

#[\Attribute('repeated')]
final class Example
{
    private const NAME = 'constant';

    private string $default = 'property';

    public function first(string $a = 'default'): string
    {
        return 'repeated' . $a . 'twice' . 'twice';
    }

    public function second(string $a = 'default'): string
    {
        $options = ['repeated' => 'repeated', 'key' => 'twice'];

        return $a . self::NAME . 'twice' . 'key' . $options['key'] . 'property' . 'property';
    }

    public function ignored(): array
    {
        return ['', '', '0', '0', '1', '1', 'a', 'a'];
    }

    public function anonymous(): object
    {
        return new class () {
            public function m(): string
            {
                return 'inner' . 'inner';
            }
        };
    }
}
