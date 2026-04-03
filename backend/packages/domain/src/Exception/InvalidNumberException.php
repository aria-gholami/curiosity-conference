<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidNumberException extends ValidationException
{
    public static function intIsInvalid(string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                \sprintf('Value for "%s" should not be -1.', $identifier),
            ),
        );
    }
}
