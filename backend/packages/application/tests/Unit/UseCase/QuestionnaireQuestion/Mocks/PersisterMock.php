<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks;

use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Persister;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;

final class PersisterMock implements Persister
{
    public Question $inputQuestion;
    public ?PersistFailedException $throwException = null;

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function persist(Question $question): void
    {
        $this->inputQuestion = $question;
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
    }
}
