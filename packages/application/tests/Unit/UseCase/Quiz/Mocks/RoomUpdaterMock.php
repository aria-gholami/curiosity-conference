<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomUpdater;

final class RoomUpdaterMock implements RoomUpdater
{
    public QuizRoom $inputQuizRoom;

    #[\Override]
    public function update(QuizRoom $quizRoom): void
    {
        $this->inputQuizRoom = $quizRoom;
    }
}
