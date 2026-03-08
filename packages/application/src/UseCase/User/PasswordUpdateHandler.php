<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\PasswordUpdateHandler as PasswordUpdateHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\PasswordUpdater;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class PasswordUpdateHandler implements PasswordUpdateHandlerInterface
{
    public function __construct(
        private PasswordUpdater $passwordUpdater,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(UserPassword $userPassword): void
    {
        $this->passwordUpdater->update($userPassword);
    }
}
