<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\JoinRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\TriviaQuiz\JoinQuizRoom;

final class JoinRoomHandlerMock implements JoinRoomHandler
{
    public JoinQuizRoom $inputJoinQuizRoom;
    public TriviaQuizRoom $outputQuizRoom;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws QuizRoomIsAlreadyStartedException
     */
    #[\Override]
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): TriviaQuizRoom
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

        return $this->outputQuizRoom;
    }
}
