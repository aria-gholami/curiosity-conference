<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuestion;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class Question
{
    public function __construct(
        public string $title,
        public array $answers,
        public int $correctAnswerIndex,
    ) {
    }
}
