<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenByUserDeleter as PasswordResetTokenByUserDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class PasswordResetTokenByUserDeleter implements PasswordResetTokenByUserDeleterInterface
{
    public function __construct(
        private PasswordResetRepository $passwordResetRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function delete(User $user): void
    {
        try {
            $this->logger->info('Start deleting all password reset token data.', [
                'email' => $user->getEmail(),
            ]);
            $this->passwordResetRepository->removeByUser($user);
            $this->logger->info('Deleting all password reset token data was successful.', [
                'email' => $user->getEmail(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting all password reset token data was not successful.', [
                'email' => $user->getEmail(),
            ]);
            $this->logger->debug('Deleting all password reset token data was not successful.', [
                'email' => $user->getEmail(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(PasswordReset::class);
        }
    }
}
