<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion as QuestionEntity;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Persister;
use CuriosityConference\Domain\TriviaQuestion\Question;

final class PersisterMock implements Persister
{
    public Question $inputQuestion;
    public QuestionEntity $outputQuestionEntity;

    #[\Override]
    public function persist(Question $question): QuestionEntity
    {
        $this->inputQuestion = $question;

        return $this->outputQuestionEntity;
    }
}
