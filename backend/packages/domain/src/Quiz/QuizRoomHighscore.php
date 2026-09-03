<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

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
