<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\CreateHandler;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\Id;
use CuriosityConference\Domain\User\Model\UserName;

final class CreateHandlerMock implements CreateHandler
{
    public UserName $inputUserName;
    public Id $outputUserId;
    public ?PersistFailedException $thrownException = null;

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(UserName $userName): Id
    {
        $this->inputUserName = $userName;
        if ($this->thrownException instanceof PersistFailedException) {
            throw $this->thrownException;
        }

        return $this->outputUserId;
    }
}
