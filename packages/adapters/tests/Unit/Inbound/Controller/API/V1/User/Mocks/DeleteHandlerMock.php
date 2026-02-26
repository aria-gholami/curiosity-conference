<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\DeleteHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Email;

final class DeleteHandlerMock implements DeleteHandler
{
    public Email $inputEmail;
    public ?DeleteFailedException $throwException = null;

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Email $email): void
    {
        $this->inputEmail = $email;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
    }
}
