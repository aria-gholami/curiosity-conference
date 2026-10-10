<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\AllRoomsHighscoreCollection;

interface ListHighscoreByAllRoomsHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(): AllRoomsHighscoreCollection;
}
