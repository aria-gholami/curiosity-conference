<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class QuizRoomHighscore
{
    public function __construct(
        public string $title,
        public int $score,
    ) {
    }
}
