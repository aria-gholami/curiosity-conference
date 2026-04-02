<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\StartHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Id;

final class StartHandlerMock implements StartHandler
{
    public Id $inputQuizRoomId;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(Id $quizRoomId): void
    {
        $this->inputQuizRoomId = $quizRoomId;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
    }
}
