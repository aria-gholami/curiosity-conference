<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class UserDetails
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
    ) {
        if ('' === $this->id) {
            throw EmptyStringException::stringIsEmpty('userDetailsId');
        }
        if ('' === $this->firstName) {
            throw EmptyStringException::stringIsEmpty('userDetailsFirstName');
        }
        if ('' === $this->lastName) {
            throw EmptyStringException::stringIsEmpty('userDetailsLastName');
        }
    }
}
