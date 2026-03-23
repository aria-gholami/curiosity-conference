<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\CreateRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\QuizRoom;

final class CreateRoomHandlerMock implements CreateRoomHandler
{
    public QuizRoom $inputQuizRoom;
    public string $outputQuizRoomId;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handleAndGet(QuizRoom $quizRoom): string
    {
        $this->inputQuizRoom = $quizRoom;
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuizRoomId;
    }
}
