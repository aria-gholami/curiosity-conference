<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomParticipantPersister;

final class RoomParticipantPersisterMock implements RoomParticipantPersister
{
    public QuizRoom $inputQuizRoom;
    public string $outputQuizRoomId;

    #[\Override]
    public function persist(QuizRoom $quizRoom): string
    {
        $this->inputQuizRoom = $quizRoom;

        return $this->outputQuizRoomId;
    }
}
