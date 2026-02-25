<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader as ByEmailLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByEmailLoader implements ByEmailLoaderInterface
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
    public function load(string $email): User
    {
        try {
            $this->logger->info('Start loading user.');
            $this->logger->debug('Start loading user.', [
                'userEmail' => $email,
            ]);
            $userEntity = $this->userRepository->findOneBy(['email' => $email]);
            if (!$userEntity instanceof User) {
                throw LoadEntityFailedException::loadFailed(User::class);
            }

            $this->logger->info('Loading user was successful.');
            $this->logger->debug('Loading user was successful.', [
                'userEmail' => $email,
            ]);

            return $userEntity;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading user was not successful.');
            $this->logger->debug('Loading user was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'userEmail' => $email,
            ]);

            throw LoadEntityFailedException::loadFailed(User::class);
        }
    }
}
