<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Device;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class Answer
{
    public function __construct(
        public Question $question,
        public array $answers,
        public int $correctAnswerIndex,
    ) {
    }
}
