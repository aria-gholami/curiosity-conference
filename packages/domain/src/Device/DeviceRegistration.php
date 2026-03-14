<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Device;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class DeviceRegistration
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $registrationToken,
        public string $userId,
    ) {
        if ('' === $this->registrationToken) {
            throw EmptyStringException::stringIsEmpty('deviceRegistrationToken');
        }
        if ('' === $this->userId) {
            throw EmptyStringException::stringIsEmpty('deviceRegistrationUserId');
        }
    }
}
