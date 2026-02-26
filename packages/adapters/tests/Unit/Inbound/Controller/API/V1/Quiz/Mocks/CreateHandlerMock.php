<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\CreateHandler;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;

final class CreateHandlerMock implements CreateHandler
{
    public Quiz $inputQuiz;
    public ?PersistFailedException $throwException = null;

    /**
     * @throws PersistFailedException
     */
    #[\Override]
    public function handle(Quiz $quiz): void
    {
        $this->inputQuiz = $quiz;
        if ($this->throwException instanceof PersistFailedException) {
            throw $this->throwException;
        }
    }
}
