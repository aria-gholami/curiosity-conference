<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Application\Port\Inbound\Quiz\NextQuestionPersistHandler;

final class NextQuestionPersistHandlerMock implements NextQuestionPersistHandler
{
    public string $inputQuizRoomId;

    #[\Override]
    public function handle(string $quizRoomId): void
    {
        $this->inputQuizRoomId = $quizRoomId;
    }
}
