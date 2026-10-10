<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByIdLoader;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;

final class QuestionByIdLoaderMock implements ByIdLoader
{
    public TriviaQuiz $inputQuiz;
    public QuestionCollection $outputQuestionCollection;

    #[\Override]
    public function load(TriviaQuiz $quiz): QuestionCollection
    {
        $this->inputQuiz = $quiz;

        return $this->outputQuestionCollection;
    }
}
