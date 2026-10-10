<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomParticipantPersister;

final class RoomParticipantPersisterMock implements RoomParticipantPersister
{
    public TriviaQuizRoom $inputQuizRoom;
    public TriviaQuizRoom $outputQuizRoom;

    #[\Override]
    public function persist(TriviaQuizRoom $quizRoom): TriviaQuizRoom
    {
        $this->inputQuizRoom = $quizRoom;

        return $this->outputQuizRoom;
    }
}
