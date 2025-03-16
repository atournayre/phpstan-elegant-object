<?php

declare(strict_types=1);

namespace Atournayre\PHPStan\ElegantObject\Factory;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

final readonly class RuleErrorFactory
{
    /**
     * @param string $message Principal error message
     * @param array<string|int, string|int> $messageParameters Parameters to insert in the message
     * @param array<string> $tips Tips to display
     */
    private function __construct(
        private string $message,
        private array $messageParameters = [],
        private array $tips = []
    ) {
    }

    /**
     * @param string $message Principal error message
     * @param array<string|int, string|int> $messageParameters Parameters to insert in the message
     * @param array<string> $tips Tips to display
     */
    public static function createErrorWithTips(
        string $message,
        array $messageParameters = [],
        array $tips = []
    ): self
    {
        return new self($message, $messageParameters, $tips);
    }

    /**
     * @return array<RuleError> Errors detected
     * @throws ShouldNotHappenException
     */
    public function errors(): array
    {
        $errorBuilder = RuleErrorBuilder::message(sprintf($this->message, ...$this->messageParameters));

        foreach ($this->tips as $tip) {
            $errorBuilder->tip($tip);
        }

        return [$errorBuilder->build()];
    }
}
