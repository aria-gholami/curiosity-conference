<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device\Mocks;

use CuriosityConference\Application\Port\Inbound\Device\DeleteHandler;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;

final class DeleteHandlerMock implements DeleteHandler
{
    public RegistrationToken $inputRegistrationToken;
    public ?DeleteFailedException $throwException = null;

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(RegistrationToken $registrationToken): void
    {
        $this->inputRegistrationToken = $registrationToken;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
    }
}
