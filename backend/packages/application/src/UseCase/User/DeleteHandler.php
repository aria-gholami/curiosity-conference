<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\ByUserNameDeleter;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\UserName;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private ByUserNameDeleter $byEmailDeleter,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(UserName $userName): void
    {
        $this->byEmailDeleter->delete($userName);
    }
}
