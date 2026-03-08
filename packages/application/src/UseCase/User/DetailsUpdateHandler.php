<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\DetailsUpdateHandler as DetailsUpdateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\DetailsUpdater;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DetailsUpdateHandler implements DetailsUpdateHandlerInterface
{
    public function __construct(
        private DetailsUpdater $detailsUpdater,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(UserDetails $userDetails): void
    {
        $this->detailsUpdater->update($userDetails);
    }
}
