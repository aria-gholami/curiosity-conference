<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\CreateHandler;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserRegistration;

final class CreateHandlerMock implements CreateHandler
{
    public UserRegistration $inputUser;
    public string $outputUserId;
    public ?PersistFailedException $thrownException = null;

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(UserRegistration $user): string
    {
        $this->inputUser = $user;
        if ($this->thrownException instanceof PersistFailedException) {
            throw $this->thrownException;
        }

        return $this->outputUserId;
    }
}
