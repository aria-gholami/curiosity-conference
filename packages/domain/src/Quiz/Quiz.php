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
        public string $description,
        public string $category,
    ) {
        if ('' === $this->title) {
            throw EmptyStringException::stringIsEmpty('quizTitle');
        }
        if ('' === $this->description) {
            throw EmptyStringException::stringIsEmpty('quizDescription');
        }
        if ('' === $this->category) {
            throw EmptyStringException::stringIsEmpty('quizCategory');
        }
    }
}
