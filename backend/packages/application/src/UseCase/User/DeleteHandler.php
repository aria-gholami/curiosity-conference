<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\ByIdDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private ByIdDeleter $byIdDeleter,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Id $userId): void
    {
        $this->byIdDeleter->delete($userId);
    }
}
