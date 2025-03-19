<?php
declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Tests\Fixtures\KeepConstructorsCodeFree;

use Assert\Assertion;

class ClassWithMixedAssertionsAndAssignments
{
    private string $username;
    private string $password;
    private array $config;

    public function __construct(string $username, string $password, array $config = [])
    {
        $this->username = $username;
        $this->password = $password;
        $this->config = $config;

        assert(strlen($username) >= 3, 'Username too short');

        \Assert\Assert::that($password)->notEmpty()->minLength(8);

        $this->assertValidConfig($config);
    }

    private function assertValidConfig(array $config): void
    {
        // This method is called by the constructor but is not itself analyzed
        if (isset($config['debug']) && !is_bool($config['debug'])) {
            throw new \InvalidArgumentException('Debug setting must be a boolean');
        }
    }
}
