<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\LoginHandler;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;

final class LoginHandlerMock implements LoginHandler
{
    public UserLogin $inputUserLogin;
    public string $outputLoginHandler;
    public ?UserLoginFailedException $throwException = null;

    /**
     * @throws UserLoginFailedException
     */
    #[\Override]
    public function handle(UserLogin $userLogin): string
    {
        $this->inputUserLogin = $userLogin;
        if ($this->throwException instanceof UserLoginFailedException) {
            throw $this->throwException;
        }

        return $this->outputLoginHandler;
    }
}
