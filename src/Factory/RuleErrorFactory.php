<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Factory;

use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

final readonly class RuleErrorFactory
{
    /**
     * @param string $message Principal error message
     * @param string $identifier Error identifier for PHPStan 2.0
     * @param array<string|int, string|int> $messageParameters Parameters to insert in the message
     * @param array<string> $tips Tips to display
     */
    private function __construct(
        private string $message,
        private string $identifier,
        private array $messageParameters = [],
        private array $tips = []
    ) {
    }

    /**
     * @param string $message Principal error message
     * @param string $identifier Error identifier for PHPStan 2.0
     * @param array<string|int, string|int> $messageParameters Parameters to insert in the message
     * @param array<string> $tips Tips to display
     */
    public static function createErrorWithTips(
        string $message,
        string $identifier,
        array $messageParameters = [],
        array $tips = []
    ): self
    {
        return new self($message, $identifier, $messageParameters, $tips);
    }

    /**
     * @return list<IdentifierRuleError> Errors detected
     * @throws ShouldNotHappenException
     */
    public function errors(): array
    {
        $errorBuilder = RuleErrorBuilder::message(sprintf($this->message, ...$this->messageParameters))
            ->identifier($this->identifier);

        foreach ($this->tips as $tip) {
            $errorBuilder->tip($tip);
        }

        return [$errorBuilder->build()];
    }
}
