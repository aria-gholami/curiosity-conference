<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\HighscoreUpdateHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateResponse;

final class HighscoreUpdateHandlerMock implements HighscoreUpdateHandler
{
    public HighscoreUpdateRequestBody $inputHighscoreUpdateRequestBody;
    public HighscoreUpdateResponse $outputHighscoreUpdateResponse;
    public ?\Exception $throwException = null;

    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     * @throws UpdateFailedException
     */
    #[\Override]
    public function handleAndGet(HighscoreUpdateRequestBody $highscoreUpdateRequestBody): HighscoreUpdateResponse
    {
        $this->inputHighscoreUpdateRequestBody = $highscoreUpdateRequestBody;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof UpdateFailedException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof CurrentQuestionNotFoundException) {
            throw $this->throwException;
        }
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }

        return $this->outputHighscoreUpdateResponse;
    }
}
