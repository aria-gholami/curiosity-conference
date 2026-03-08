<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\PasswordResetMailSendHandler as PasswordResetMailSendHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetMailSender;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenByUserDeleter;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenPersister;
use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\User\Model\Email;
use Psr\Log\LoggerInterface;

final readonly class PasswordResetMailSendHandler implements PasswordResetMailSendHandlerInterface
{
    public function __construct(
        private ByEmailLoader $byEmailLoader,
        private PasswordResetTokenByUserDeleter $passwordResetTokenByUserDeleter,
        private PasswordResetTokenPersister $passwordResetTokenPersister,
        private PasswordResetMailSender $passwordResetMailSender,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CouldNotSentMailException
     */
    #[\Override]
    public function handle(Email $email): void
    {
        try {
            $user = $this->byEmailLoader->load($email->value);
            $this->passwordResetTokenByUserDeleter->delete($user);
            $token = $this->passwordResetTokenPersister->persist($user);
            $this->passwordResetMailSender->send($user, $token);
        } catch (\Throwable $exception) {
            $this->logger->notice('Password reset mail data is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw CouldNotSentMailException::mailNotSent('passwordReset');
        }
    }
}
