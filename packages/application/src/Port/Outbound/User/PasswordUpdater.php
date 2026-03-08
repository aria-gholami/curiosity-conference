<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;

interface PasswordUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(UserPassword $userPassword): void;
}
