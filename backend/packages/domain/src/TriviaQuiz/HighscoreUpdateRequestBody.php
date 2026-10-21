<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class HighscoreUpdateRequestBody
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $quizRoomId,
        public string $answerId,
    ) {
        if ('' === $this->quizRoomId) {
            throw EmptyStringException::stringIsEmpty('continueOrFinishRequestBodyQuizRoomId');
        }
        if ('' === $this->answerId) {
            throw EmptyStringException::stringIsEmpty('continueOrFinishRequestBodyAnswerId');
        }
    }
}
