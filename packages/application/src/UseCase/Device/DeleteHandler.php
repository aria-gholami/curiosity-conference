<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Device;

use CuriosityConference\Application\Port\Inbound\Device\DeleteHandler as DeleteHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Device\ByRegistrationTokenDeleter;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private ByRegistrationTokenDeleter $byRegistrationTokenDeleter,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(RegistrationToken $registrationToken): void
    {
        $this->byRegistrationTokenDeleter->delete($registrationToken);
    }
}
