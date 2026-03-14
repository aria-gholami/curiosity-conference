<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Device;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Device;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Device\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $deviceEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(string $registrationToken, User $user): void
    {
        try {
            $this->logger->info('Start persisting device.', [
                'registrationToken' => $registrationToken,
                'userId' => $user->getId(),
            ]);
            $device = new Device(
                Uuid::v4()->toRfc4122(),
                $registrationToken,
                $user,
            );
            $this->deviceEntityManager->persist($device);
            $this->deviceEntityManager->flush();

            $this->logger->info('Persisting device was successful.', [
                'registrationToken' => $registrationToken,
                'userId' => $user->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting device was not successful.', [
                'registrationToken' => $registrationToken,
                'userId' => $user->getId(),
            ]);
            $this->logger->debug('Persisting device was not successful.', [
                'registrationToken' => $registrationToken,
                'userId' => $user->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(Device::class);
        }
    }
}
