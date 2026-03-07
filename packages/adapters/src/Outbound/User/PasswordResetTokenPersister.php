<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenPersister as PasswordResetTokenPersisterInterface;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class PasswordResetTokenPersister implements PasswordResetTokenPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $passwordResetEntityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(User $user): int
    {
        try {
            $this->logger->info('Start persisting password reset token.', [
                'userId' => $user->getId(),
            ]);
            $passwordResetEntity = new PasswordReset(
                Uuid::v4()->toRfc4122(),
                $user,
                $this->generatePasswordResetToken(),
                new \DateTimeImmutable()->modify('+30 minutes'),
                null,
                new \DateTimeImmutable(),
            );
            $this->passwordResetEntityManager->persist($passwordResetEntity);
            $this->passwordResetEntityManager->flush();

            $this->logger->info('Persisting password reset token was successful.', [
                'userId' => $user->getId(),
            ]);

            return $passwordResetEntity->getToken();
        } catch (\Throwable $exception) {
            $this->logger->notice('Persisting password reset token was not successful.', [
                'userId' => $user->getId(),
            ]);
            $this->logger->debug('Persisting password reset token was not successful.', [
                'userId' => $user->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw PersistFailedException::persistFailed(PasswordReset::class);
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @infection-ignore-all
     */
    private function generatePasswordResetToken(): int
    {
        return \random_int(10000000, 99999999);
    }
}
