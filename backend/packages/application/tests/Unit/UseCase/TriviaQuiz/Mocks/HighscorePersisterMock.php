<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscorePersister;

final class HighscorePersisterMock implements HighscorePersister
{
    public TriviaQuizRoom $inputQuizRoom;

    #[\Override]
    public function persist(TriviaQuizRoom $quizRoom): void
    {
        $this->inputQuizRoom = $quizRoom;
    }
}
