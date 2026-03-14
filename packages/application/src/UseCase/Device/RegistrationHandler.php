<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Device;

use CuriosityConference\Application\Port\Inbound\Device\RegistrationHandler as RegistrationHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Device\Persister;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader;
use CuriosityConference\Domain\Device\DeviceRegistration;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class RegistrationHandler implements RegistrationHandlerInterface
{
    public function __construct(
        private ByIdLoader $byIdLoader,
        private Persister $persister,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(DeviceRegistration $deviceRegistration): void
    {
        $user = $this->byIdLoader->load($deviceRegistration->userId);
        $this->persister->persist($deviceRegistration->registrationToken, $user);
    }
}
