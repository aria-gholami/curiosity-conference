<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreUpdater;

final class HighscoreUpdaterMock implements HighscoreUpdater
{
    public TriviaHighscore $inputHighscore;

    #[\Override]
    public function update(TriviaHighscore $highscore): void
    {
        $this->inputHighscore = $highscore;
    }
}
