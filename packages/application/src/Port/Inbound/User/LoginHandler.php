<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use CuriosityConference\Domain\User\Model\UserLoginResponse;

interface LoginHandler
{
    /**
     * @throws UserLoginFailedException
     */
    public function handle(UserLogin $userLogin): UserLoginResponse;
}
