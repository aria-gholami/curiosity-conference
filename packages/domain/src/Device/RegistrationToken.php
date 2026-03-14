<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Device;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class RegistrationToken
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value) {
            throw EmptyStringException::stringIsEmpty('registrationToken');
        }
    }
}
