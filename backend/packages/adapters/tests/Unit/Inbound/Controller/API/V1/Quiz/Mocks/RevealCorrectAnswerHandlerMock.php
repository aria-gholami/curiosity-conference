<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\RevealCorrectAnswerHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;

final class RevealCorrectAnswerHandlerMock implements RevealCorrectAnswerHandler
{
    public Id $inputQuizRoomId;
    public ?LoadEntityFailedException $throwException = null;

    public function handle(Id $quizRoomId): void
    {
        $this->inputQuizRoomId = $quizRoomId;
        if ($this->throwException instanceof LoadEntityFailedException) {
            throw $this->throwException;
        }
    }
}
