<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Application\Port\Outbound\Question\ByQuizDeleter;

final class ByQuizDeleterMock implements ByQuizDeleter
{
    public Quiz $inputQuiz;

    #[\Override]
    public function delete(Quiz $quiz): void
    {
        $this->inputQuiz = $quiz;
    }
}
