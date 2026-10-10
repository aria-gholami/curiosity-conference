<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizCollection;

final class LoaderMock implements Loader
{
    public QuizCollection $outputQuizCollection;
    public ?\Exception $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): QuizCollection
    {
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuizCollection;
    }
}
