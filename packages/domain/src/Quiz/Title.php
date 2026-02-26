<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class Title
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value) {
            throw EmptyStringException::stringIsEmpty('quizTitle');
        }
    }
}
