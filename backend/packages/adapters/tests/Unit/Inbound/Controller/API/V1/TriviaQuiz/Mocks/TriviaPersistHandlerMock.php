<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\TriviaPersistHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;

final class TriviaPersistHandlerMock implements TriviaPersistHandler
{
    public array $inputQuizzes;
    public ?\Exception $throwException = null;

    /**
     * @throws CorrectAnswerNotExistingException
     * @throws DeleteFailedException
     * @throws EmptyStringException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(array $quizzes): void
    {
        $this->inputQuizzes = $quizzes;
        if ($this->throwException instanceof EmptyStringException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof CorrectAnswerNotExistingException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof DeleteFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
    }
}
