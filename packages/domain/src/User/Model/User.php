<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;

final readonly class User
{
    public const string EMAIL_REGEX = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';

    /**
     * @throws EmptyStringException
     * @throws InvalidEmailException
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
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
    }
}
