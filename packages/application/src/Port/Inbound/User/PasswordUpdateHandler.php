<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;

interface PasswordUpdateHandler
{
    /**
     * @throws UpdateFailedException
     */
    public function handle(UserPassword $userPassword): void;
}
