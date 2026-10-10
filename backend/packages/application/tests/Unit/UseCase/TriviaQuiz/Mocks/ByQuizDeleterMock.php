<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByQuizDeleter;

final class ByQuizDeleterMock implements ByQuizDeleter
{
    public TriviaQuiz $inputQuiz;

    #[\Override]
    public function delete(TriviaQuiz $quiz): void
    {
        $this->inputQuiz = $quiz;
    }
}
