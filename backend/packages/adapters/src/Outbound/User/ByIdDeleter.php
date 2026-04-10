<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\ByIdDeleter as ByIdDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;
use Psr\Log\LoggerInterface;

final readonly class ByIdDeleter implements ByIdDeleterInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(Id $userId): void
    {
        try {
            $this->logger->info('Start deleting user.');
            $this->logger->debug('Start deleting user.', [
                'userId' => $userId->value,
            ]);
            $userEntity = $this->userRepository->findOneBy([
                'id' => $userId->value,
            ]);
            if (!$userEntity instanceof User) {
                return;
            }

            $this->userRepository->remove($userEntity);

            $this->logger->info('Deleting user was successful.');
            $this->logger->debug('Deleting user was successful.', [
                'userId' => $userId->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting user was not successful.');
            $this->logger->debug('Deleting user was not successful.', [
                'userId' => $userId->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(User::class);
        }
    }
}
