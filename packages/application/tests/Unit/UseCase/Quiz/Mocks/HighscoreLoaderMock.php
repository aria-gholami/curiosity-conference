<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader;

final class HighscoreLoaderMock implements HighscoreLoader
{
    public string $inputUserId;
    public string $inputQuizRoomId;
    public Highscore $outputHighscore;

    #[\Override]
    public function load(string $userId, string $quizRoomId): Highscore
    {
        $this->inputUserId = $userId;
        $this->inputQuizRoomId = $quizRoomId;

        return $this->outputHighscore;
    }
}
