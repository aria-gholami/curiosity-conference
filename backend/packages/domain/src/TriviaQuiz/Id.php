<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;

final readonly class Id
{
    /**
     * @throws EmptyStringException
     */
    public function __construct(
        public string $value,
    ) {
        if ('' === $this->value) {
            throw EmptyStringException::stringIsEmpty('id');
        }
    }
}
