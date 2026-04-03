<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class QuizRoomParticipant
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $quizRoomId,
        public string $participantId,
    ) {
        if ('' === $this->quizRoomId) {
            throw EmptyStringException::stringIsEmpty('quizRoomParticipantQuizRoomId');
        }
        if ('' === $this->participantId) {
            throw EmptyStringException::stringIsEmpty('quizRoomParticipantId');
        }
    }
}
