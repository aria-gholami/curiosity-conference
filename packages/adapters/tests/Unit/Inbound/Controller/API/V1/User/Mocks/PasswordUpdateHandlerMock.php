<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\PasswordUpdateHandler;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;

final class PasswordUpdateHandlerMock implements PasswordUpdateHandler
{
    public UserPassword $inputUserPassword;
    public ?UpdateFailedException $throwException = null;

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handle(UserPassword $userPassword): void
    {
        $this->inputUserPassword = $userPassword;
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
    }
}
