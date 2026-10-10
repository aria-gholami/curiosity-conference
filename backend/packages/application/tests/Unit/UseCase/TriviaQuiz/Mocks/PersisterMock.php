<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Persister;
use CuriosityConference\Domain\TriviaQuiz\Quiz;

final class PersisterMock implements Persister
{
    public Quiz $inputQuiz;
    public string $outputId;

    #[\Override]
    public function persist(Quiz $quiz): string
    {
        $this->inputQuiz = $quiz;

        return $this->outputId;
    }
}
