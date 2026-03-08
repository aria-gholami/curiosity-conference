<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class InvalidPasswordException extends ValidationException
{
    public static function passwordNotSameAsConfirmPassword(string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                'Password is not the same as confirmPassword.',
            ),
        );
    }

    public static function invalidPassword(string $password, string $identifier): self
    {
        return new self(
            parent::buildMessage(
                $identifier,
                \sprintf('Password "%s" is not valid: .', $password),
            ),
        );
    }
}
