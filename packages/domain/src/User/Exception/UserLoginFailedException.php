<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Exception;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final class UserLoginFailedException extends \Exception
{
    public static function loginFailed(string $email): self
    {
        return new self(
            \sprintf('Login user "%s" was not successful.', $email),
        );
    }
}
