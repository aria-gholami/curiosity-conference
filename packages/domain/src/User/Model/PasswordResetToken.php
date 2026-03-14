<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Exception\InvalidPasswordResetTokenException;

final readonly class PasswordResetToken
{
    /**
     * @throws EmptyStringException
     * @throws InvalidPasswordResetTokenException
     */
    public function __construct(
        public int $token,
        public string $email,
    ) {
        if ('' === $this->email) {
            throw EmptyStringException::stringIsEmpty('passwordResetTokenUserEmail');
        }
        if ($this->token < 10000000 || $this->token > 99999999) {
            throw InvalidPasswordResetTokenException::invalidToken(
                'passwordReset',
            );
        }
    }
}
