<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Device;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Device;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\DeviceRepository;
use CuriosityConference\Application\Port\Outbound\Device\ByRegistrationTokenDeleter as ByRegistrationTokenDeleterInterface;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByRegistrationTokenDeleter implements ByRegistrationTokenDeleterInterface
{
    public function __construct(
        private DeviceRepository $deviceRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(RegistrationToken $registrationToken): void
    {
        try {
            $this->logger->info('Start deleting device.', [
                'registrationToken' => $registrationToken->value,
            ]);
            $device = $this->deviceRepository->findOneBy([
                'registrationToken' => $registrationToken->value,
            ]);
            if (!$device instanceof Device) {
                return;
            }

            $this->deviceRepository->remove($device);

            $this->logger->info('Deleting device was successful.', [
                'registrationToken' => $registrationToken->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting device was not successful.', [
                'registrationToken' => $registrationToken->value,
            ]);
            $this->logger->debug('Deleting device was not successful.', [
                'registrationToken' => $registrationToken->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(Device::class);
        }
    }
}
