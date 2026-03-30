<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface HighscoreLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $userId, string $quizRoomId): Highscore;
}
