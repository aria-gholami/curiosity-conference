<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Question\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;
use CuriosityConference\Application\Port\Outbound\Question\Persister;
use CuriosityConference\Domain\Question\Question;

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
