<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class ContinueOrFinishResponse
{
    public function __construct(
        public Highscore $highscore,
        public Answer $answer,
    ) {
    }
}
