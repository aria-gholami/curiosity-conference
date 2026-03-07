<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Exception;

use CuriosityConference\Domain\Exception\ValidationException;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidPasswordResetTokenException extends ValidationException
{
    public static function invalidToken(string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                \sprintf('Token %s must be exactly 8 digits.', $identifier),
            ),
        );
    }
}
