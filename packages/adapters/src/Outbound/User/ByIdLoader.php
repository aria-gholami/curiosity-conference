<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $id): User
    {
        try {
            $this->logger->info('Start loading user.', [
                'userId' => $id,
            ]);
            $userEntity = $this->userRepository->findOneBy(['id' => $id]);
            if (!$userEntity instanceof User) {
                throw LoadEntityFailedException::loadFailed(User::class);
            }

            $this->logger->info('Loading user was successful.', [
                'userId' => $id,
            ]);

            return $userEntity;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading user was not successful.', [
                'userId' => $id,
            ]);
            $this->logger->debug('Loading user was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'userId' => $id,
            ]);

            throw LoadEntityFailedException::loadFailed(User::class);
        }
    }
}
