<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Domain\Exception\UpdateFailedException;

interface HighscoreUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(Highscore $highscore): void;
}
