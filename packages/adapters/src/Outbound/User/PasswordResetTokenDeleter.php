<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenDeleter as PasswordResetTokenDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class PasswordResetTokenDeleter implements PasswordResetTokenDeleterInterface
{
    public function __construct(
        private PasswordResetRepository $passwordResetRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(PasswordReset $passwordReset): void
    {
        try {
            $this->logger->info('Start deleting password reset token data.', [
                'email' => $passwordReset->getUser()->getEmail(),
                'token' => $passwordReset->getToken(),
            ]);
            $this->passwordResetRepository->remove($passwordReset);
            $this->logger->info('Deleting password reset token data was successful.', [
                'email' => $passwordReset->getUser()->getEmail(),
                'token' => $passwordReset->getToken(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting password reset token data was not successful.', [
                'email' => $passwordReset->getUser()->getEmail(),
                'token' => $passwordReset->getToken(),
            ]);
            $this->logger->debug('Deleting password reset token data was not successful.', [
                'email' => $passwordReset->getUser()->getEmail(),
                'token' => $passwordReset->getToken(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(PasswordReset::class);
        }
    }
}
