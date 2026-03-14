<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Device;

use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface DeleteHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(RegistrationToken $registrationToken): void;
}
