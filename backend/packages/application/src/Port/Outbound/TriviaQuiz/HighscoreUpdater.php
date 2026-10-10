<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Domain\Exception\UpdateFailedException;

interface HighscoreUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(TriviaHighscore $highscore): void;
}
