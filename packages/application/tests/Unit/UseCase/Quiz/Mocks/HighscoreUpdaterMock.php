<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreUpdater;

final class HighscoreUpdaterMock implements HighscoreUpdater
{
    public Highscore $inputHighscore;

    #[\Override]
    public function update(Highscore $highscore): void
    {
        $this->inputHighscore = $highscore;
    }
}
