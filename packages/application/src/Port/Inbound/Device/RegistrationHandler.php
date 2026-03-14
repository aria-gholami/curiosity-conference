<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Device;

use CuriosityConference\Domain\Device\DeviceRegistration;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface RegistrationHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handle(DeviceRegistration $deviceRegistration): void;
}
