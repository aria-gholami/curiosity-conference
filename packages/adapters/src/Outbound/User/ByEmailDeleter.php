<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\ByEmailDeleter as ByEmailDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Email;
use Psr\Log\LoggerInterface;

final readonly class ByEmailDeleter implements ByEmailDeleterInterface
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
    public function delete(Email $email): void
    {
        try {
            $this->logger->info('Start deleting user.');
            $this->logger->debug('Start deleting user.', [
                'email' => $email->value,
            ]);
            $quizEntity = $this->userRepository->findOneBy([
                'email' => $email->value,
            ]);
            if (!$quizEntity instanceof User) {
                return;
            }

            $this->userRepository->remove($quizEntity);

            $this->logger->info('Deleting user was successful.');
            $this->logger->debug('Deleting user was successful.', [
                'email' => $email->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting user was not successful.');
            $this->logger->debug('Deleting user was not successful.', [
                'email' => $email->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(User::class);
        }
    }
}
