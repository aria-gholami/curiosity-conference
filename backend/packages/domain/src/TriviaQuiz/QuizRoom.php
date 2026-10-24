<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\TriviaQuiz\Exception\InvalidRoomTitleException;

final readonly class QuizRoom
{
    /**
     * @throws EmptyStringException
     * @throws InvalidRoomTitleException
     */
    public function __construct(
        public string $hostId,
        public string $title,
    ) {
        if ('' === $this->hostId) {
            throw EmptyStringException::stringIsEmpty('quizRoomHostId');
        }
        if ('' === $this->title || \mb_strlen($this->title) > 15) {
            throw InvalidRoomTitleException::isInvalid('quizRoomTitle');
        }
    }
}
