<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\AllRoomsHighscoreCollection;

interface HighscoreByAllRoomsLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): AllRoomsHighscoreCollection;
}
