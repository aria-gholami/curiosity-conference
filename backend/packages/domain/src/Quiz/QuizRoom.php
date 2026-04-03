<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class QuizRoom
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $quizId,
        public string $hostId,
    ) {
        if ('' === $this->quizId) {
            throw EmptyStringException::stringIsEmpty('quizRoomQuizId');
        }
        if ('' === $this->hostId) {
            throw EmptyStringException::stringIsEmpty('quizRoomHostId');
        }
    }
}
