<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Question\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Application\Port\Outbound\Question\ByIdLoader;
use CuriosityConference\Domain\Question\QuestionCollection;

final class QuestionByIdLoaderMock implements ByIdLoader
{
    public Quiz $inputQuiz;
    public QuestionCollection $outputQuestionCollection;

    #[\Override]
    public function load(Quiz $quiz): QuestionCollection
    {
        $this->inputQuiz = $quiz;

        return $this->outputQuestionCollection;
    }
}
