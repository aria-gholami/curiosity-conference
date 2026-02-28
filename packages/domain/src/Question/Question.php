<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class Question
{
    public function __construct(
        public Quiz $quiz,
        public string $title,
    ) {
    }
}
