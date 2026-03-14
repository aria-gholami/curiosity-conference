<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Application\Port\Inbound\User\PasswordResetTokenVerifyHandler as PasswordResetTokenVerifyHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenDeleter;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenLoader;
use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use Psr\Log\LoggerInterface;

final readonly class PasswordResetTokenVerifyHandler implements PasswordResetTokenVerifyHandlerInterface
{
    public function __construct(
        private ByEmailLoader $byEmailLoader,
        private PasswordResetTokenLoader $passwordResetTokenLoader,
        private PasswordResetTokenDeleter $passwordResetTokenDeleter,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PasswordResetTokenVerifyFailedException
     */
    #[\Override]
    public function handleAndGet(PasswordResetToken $passwordResetToken): bool
    {
        try {
            $user = $this->byEmailLoader->load($passwordResetToken->email);
            $passwordReset = $this->passwordResetTokenLoader->load($user, $passwordResetToken);
            if (!$this->isPasswordResetTokenValid($passwordReset)) {
                return false;
            }

            $this->passwordResetTokenDeleter->delete($passwordReset);

            return true;
        } catch (\Throwable $exception) {
            $this->logger->notice('Password reset token verification failed.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);
            throw PasswordResetTokenVerifyFailedException::verifyFailed();
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @infection-ignore-all
     */
    private function isPasswordResetTokenValid(?PasswordReset $passwordReset): bool
    {
        return $passwordReset instanceof PasswordReset
            && new \DateTimeImmutable() <= $passwordReset->getExpiresAt();
    }
}
