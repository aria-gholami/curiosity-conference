<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ListRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomCollection;

final class ListRoomHandlerMock implements ListRoomHandler
{
    public QuizRoomCollection $outputQuizRoomCollection;
    public ?LoadEntityFailedException $throwException = null;

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(): QuizRoomCollection
    {
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }

        return $this->outputQuizRoomCollection;
    }
}
