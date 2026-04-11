<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class Quiz
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $title,
    ) {
        if ('' === $this->title) {
            throw EmptyStringException::stringIsEmpty('quizTitle');
        }
    }
}
