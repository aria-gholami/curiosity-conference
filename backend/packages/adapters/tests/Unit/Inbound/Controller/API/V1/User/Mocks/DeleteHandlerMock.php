<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\DeleteHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;

final class DeleteHandlerMock implements DeleteHandler
{
    public Id $inputUserId;
    public ?DeleteFailedException $throwException = null;

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function handle(Id $userId): void
    {
        $this->inputUserId = $userId;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
    }
}
