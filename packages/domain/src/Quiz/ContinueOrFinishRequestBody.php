<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class ContinueOrFinishRequestBody
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
            throw EmptyStringException::stringIsEmpty('continueOrFinishRequestBodyQuizRoomId');
        }
        if ('' === $this->userId) {
            throw EmptyStringException::stringIsEmpty('continueOrFinishRequestBodyUserId');
        }
        if ('' === $this->answerId) {
            throw EmptyStringException::stringIsEmpty('continueOrFinishRequestBodyAnswerId');
        }
    }
}
