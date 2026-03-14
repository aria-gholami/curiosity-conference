<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device\Mocks;

use CuriosityConference\Application\Port\Inbound\Device\RegistrationHandler;
use CuriosityConference\Domain\Device\DeviceRegistration;

final class RegistrationHandlerMock implements RegistrationHandler
{
    public DeviceRegistration $inputDeviceRegistration;
    public \Exception|null $throwException = null;

    /**
     * @throws \Exception
     */
    #[\Override]
    public function handle(DeviceRegistration $deviceRegistration): void
    {
        $this->inputDeviceRegistration = $deviceRegistration;
        if ($this->throwException instanceof \Exception) {
            throw $this->throwException;
        }
    }
}
