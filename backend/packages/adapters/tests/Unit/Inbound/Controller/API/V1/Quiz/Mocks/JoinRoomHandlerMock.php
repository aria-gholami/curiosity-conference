<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\JoinRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;

final class JoinRoomHandlerMock implements JoinRoomHandler
{
    public JoinQuizRoom $inputJoinQuizRoom;
    public string $outputJoinQuizRoomId;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws QuizRoomIsAlreadyStartedException
     */
    #[\Override]
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): string
    {
        $this->inputJoinQuizRoom = $joinQuizRoom;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof QuizRoomIsAlreadyStartedException) {
            throw $this->throwException;
        }

        return $this->outputJoinQuizRoomId;
    }
}
