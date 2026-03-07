<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class PasswordResetTokenVerifyFailedException extends \Exception
{
    public static function verifyFailed(): self
    {
        return new self(
            'Password reset token verification failed.',
        );
    }
}
