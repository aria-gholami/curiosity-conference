<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class HighscoreUpdateResponse
{
    public function __construct(
        public TriviaHighscore $highscore,
        public TriviaAnswer $answer,
    ) {
    }
}
