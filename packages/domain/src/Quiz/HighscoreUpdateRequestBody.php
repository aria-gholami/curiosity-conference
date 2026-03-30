<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class HighscoreUpdateRequestBody
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $quizRoomId,
        public string $userId,
        public string $answerId,
    ) {
        if ('' === $this->quizRoomId) {
            throw EmptyStringException::stringIsEmpty('quizHighscoreQuizRoomId');
        }
        if ('' === $this->userId) {
            throw EmptyStringException::stringIsEmpty('quizHighscoreUserId');
        }
        if ('' === $this->answerId) {
            throw EmptyStringException::stringIsEmpty('quizHighscoreAnswerId');
        }
    }
}
