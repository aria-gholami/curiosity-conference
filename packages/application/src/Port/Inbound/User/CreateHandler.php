<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserLoginResponse;
use CuriosityConference\Domain\User\Model\UserRegistration;

interface CreateHandler
{
    /**
     * @throws PersistFailedException
     */
    public function handle(UserRegistration $user): UserLoginResponse;
}
