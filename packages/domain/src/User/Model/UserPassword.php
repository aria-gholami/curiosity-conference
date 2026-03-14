<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;

final readonly class UserPassword
{
    public const string PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    /**
     * @throws EmptyStringException
     * @throws InvalidPasswordException
     */
    public function __construct(
        public string $email,
        public string $password,
        public string $confirmPassword,
    ) {
        if ('' === $this->email) {
            throw EmptyStringException::stringIsEmpty('userPasswordEmail');
        }
        if ('' === $this->password) {
            throw EmptyStringException::stringIsEmpty('userPasswordPassword');
        }
        if ('' === $this->confirmPassword) {
            throw EmptyStringException::stringIsEmpty('userPasswordConfirmPassword');
        }
        if ($this->password !== $this->confirmPassword) {
            throw InvalidPasswordException::passwordNotSameAsConfirmPassword('userPasswordConfirmPassword');
        }
        if (!\preg_match(self::PASSWORD_REGEX, $this->password)) {
            throw InvalidPasswordException::invalidPassword($this->password, 'userPasswordPassword');
        }
    }
}
