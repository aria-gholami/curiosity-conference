<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Application\Port\Outbound\User\DetailsUpdater as DetailsUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;
use Psr\Log\LoggerInterface;

final readonly class DetailsUpdater implements DetailsUpdaterInterface
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
    public function update(UserDetails $userDetails): void
    {
        try {
            $this->logger->info('Start updating user details.', [
                'userId' => $userDetails->id,
            ]);
            $this->userRepository->updateNameById($userDetails);

            $this->logger->info('Updating user details was successful.', [
                'userId' => $userDetails->id,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating user details was not successful.', [
                'userId' => $userDetails->id,
            ]);
            $this->logger->debug('Updating user details was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
                'userId' => $userDetails->id,
            ]);

            throw UpdateFailedException::updateFailed(User::class);
        }
    }
}
