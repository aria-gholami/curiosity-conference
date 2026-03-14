<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Device;

use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface ByRegistrationTokenDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(RegistrationToken $registrationToken): void;
}
