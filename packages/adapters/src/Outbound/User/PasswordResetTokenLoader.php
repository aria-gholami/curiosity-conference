<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenLoader as PasswordResetTokenLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use Psr\Log\LoggerInterface;

final readonly class PasswordResetTokenLoader implements PasswordResetTokenLoaderInterface
{
    public function __construct(
        private PasswordResetRepository $passwordResetRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(User $user, PasswordResetToken $passwordResetToken): ?PasswordReset
    {
        try {
            $this->logger->info('Start loading password reset token data.', [
                'userId' => $passwordResetToken->userId,
                'token' => $passwordResetToken->token,
            ]);
            $passwordReset = $this->passwordResetRepository->findOneBy(['user' => $user, 'token' => $passwordResetToken->token]);
            $this->logger->info('Loading password reset token data was successful.', [
                'userId' => $passwordResetToken->userId,
                'token' => $passwordResetToken->token,
            ]);

            return $passwordReset;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading password reset token data was not successful.', [
                'userId' => $passwordResetToken->userId,
                'token' => $passwordResetToken->token,
            ]);
            $this->logger->debug('Loading password reset token data was not successful.', [
                'userId' => $passwordResetToken->userId,
                'token' => $passwordResetToken->token,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(PasswordReset::class);
        }
    }
}
