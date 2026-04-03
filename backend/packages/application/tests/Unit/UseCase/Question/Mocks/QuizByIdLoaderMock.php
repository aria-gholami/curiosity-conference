<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Question\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Application\Port\Outbound\Quiz\ByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;

final class QuizByIdLoaderMock implements ByIdLoader
{
    public Id $inputId;
    public Quiz $outputQuiz;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(Id $id): Quiz
    {
        $this->inputId = $id;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuiz;
    }
}
