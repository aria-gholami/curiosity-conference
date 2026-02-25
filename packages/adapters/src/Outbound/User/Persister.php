<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User as UserEntity;
use CuriosityConference\Application\Port\Outbound\User\Persister as PersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class Persister implements PersisterInterface
{
    public function __construct(
        private EntityManagerInterface $userEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(User $user): string
    {
        try {
            $this->logger->info('Start persisting user.');
            $this->logger->debug('Start persisting user.', [
                'userFirstName' => $user->firstName,
                'userLastName' => $user->lastName,
                'userEmail' => $user->email,
            ]);
            $userEntity = new UserEntity(
                Uuid::v4()->toRfc4122(),
                $user->firstName,
                $user->lastName,
                $user->email,
                $user->password,
            );
            $this->userEntityManager->persist($userEntity);
            $this->userEntityManager->flush();

            $this->logger->info('Persisting user was successful.');
            $this->logger->debug('Persisting user was successful.', [
                'userFirstName' => $user->firstName,
                'userLastName' => $user->lastName,
                'userEmail' => $user->email,
            ]);

            return $userEntity->getId();
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting user was not successful.');
            $this->logger->debug('Persisting user was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'userFirstName' => $user->firstName,
                'userLastName' => $user->lastName,
                'userEmail' => $user->email,
            ]);

            throw PersistFailedException::persistFailed(UserEntity::class);
        }
    }
}
