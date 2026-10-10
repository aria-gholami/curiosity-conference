<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class JoinQuizRoom
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $quizRoomId,
        public string $userId,
    ) {
        if ('' === $this->quizRoomId) {
            throw EmptyStringException::stringIsEmpty('joinQuizRoomId');
        }
        if ('' === $this->userId) {
            throw EmptyStringException::stringIsEmpty('joinQuizRoomUserId');
        }
    }
}
