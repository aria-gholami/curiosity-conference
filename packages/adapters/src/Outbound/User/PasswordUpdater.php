<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\PasswordUpdater as PasswordUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;
use Psr\Log\LoggerInterface;

final readonly class PasswordUpdater implements PasswordUpdaterInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function update(UserPassword $userPassword): void
    {
        try {
            $this->logger->info('Start updating user password.', [
                'userEmail' => $userPassword->email,
            ]);
            $this->userRepository->updatePasswordByEmail($userPassword);

            $this->logger->info('Updating user password was successful.', [
                'userEmail' => $userPassword->email,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating user password was not successful.', [
                'userEmail' => $userPassword->email,
            ]);
            $this->logger->debug('Updating user password was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'userEmail' => $userPassword->email,
            ]);

            throw UpdateFailedException::updateFailed(User::class);
        }
    }
}
