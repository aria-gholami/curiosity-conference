<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class Question
{
    public function __construct(
        public TriviaQuiz $quiz,
        public string $title,
        public array $answers,
        public int $correctAnswerIndex,
    ) {
    }
}
