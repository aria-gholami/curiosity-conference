<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomUpdater;

final class RoomUpdaterMock implements RoomUpdater
{
    public TriviaQuizRoom $inputQuizRoom;

    #[\Override]
    public function update(TriviaQuizRoom $quizRoom): void
    {
        $this->inputQuizRoom = $quizRoom;
    }
}
