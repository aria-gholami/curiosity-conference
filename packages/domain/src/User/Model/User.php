<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;

final readonly class User
{
    public const string EMAIL_REGEX = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';
    public const string PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    /**
     * @throws EmptyStringException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public string $confirmPassword,
    ) {
        if ('' === $this->firstName) {
            throw EmptyStringException::stringIsEmpty('userFirstName');
        }
        if ('' === $this->lastName) {
            throw EmptyStringException::stringIsEmpty('userLastName');
        }
        if ('' === $this->email) {
            throw EmptyStringException::stringIsEmpty('userEmail');
        }
        if (!\preg_match(self::EMAIL_REGEX, $this->email)) {
            throw InvalidEmailException::emailIsInvalid('user');
        }
        if ('' === $this->password) {
            throw EmptyStringException::stringIsEmpty('userPassword');
        }
        if ('' === $this->confirmPassword) {
            throw EmptyStringException::stringIsEmpty('userConfirmPassword');
        }
        if ($this->password !== $this->confirmPassword) {
            throw InvalidPasswordException::passwordNotSameAsConfirmPassword();
        }
        if (!\preg_match(self::PASSWORD_REGEX, $this->password)) {
            throw InvalidPasswordException::invalidPassword($this->password);
        }
    }
}
