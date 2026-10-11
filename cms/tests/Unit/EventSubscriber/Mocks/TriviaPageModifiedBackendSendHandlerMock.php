<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber\Mocks;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use CuriosityConference\Core\Trivia\PageModifiedBackendSendHandler;

final class TriviaPageModifiedBackendSendHandlerMock implements PageModifiedBackendSendHandler
{
    public array $inputQuizzes;
    public ?CouldNotSendTriviaModifiedDataException $throwCouldNotSendTriviaModifiedDataException = null;

    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    #[\Override]
    public function handle(array $quizzes): void
    {
        $this->inputQuizzes = $quizzes;
        if ($this->throwCouldNotSendTriviaModifiedDataException instanceof CouldNotSendTriviaModifiedDataException) {
            throw $this->throwCouldNotSendTriviaModifiedDataException;
        }
    }
}
