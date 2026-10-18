<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion\Mocks;

use CuriosityConference\Application\Port\Inbound\QuestionnaireQuestion\PersistHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpsertFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;

final class PersistHandlerMock implements PersistHandler
{
    public array $inputQuestions;
    public bool $inputShowResults;
    public ?\Exception $throwException = null;

    /**
     * @throws DeleteFailedException
     * @throws EmptyAnswerArrayException
     * @throws EmptyStringException
     * @throws PersistFailedException
     * @throws UpsertFailedException
     */
    #[\Override]
    public function handle(array $questions, bool $showResults): void
    {
        $this->inputQuestions = $questions;
        $this->inputShowResults = $showResults;
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof EmptyStringException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof EmptyAnswerArrayException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpsertFailedException) {
            throw $this->throwException;
        }
    }
}
