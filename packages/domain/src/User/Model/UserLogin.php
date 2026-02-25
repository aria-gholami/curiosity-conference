<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class UserLogin
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $email,
        public string $password,
    ) {
        if ('' === $this->email) {
            throw EmptyStringException::stringIsEmpty('userLoginEmail');
        }
        if ('' === $this->password) {
            throw EmptyStringException::stringIsEmpty('userLoginPassword');
        }
    }
}
