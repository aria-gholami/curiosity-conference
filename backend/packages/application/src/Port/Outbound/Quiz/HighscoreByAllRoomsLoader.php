<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\AllRoomsHighscoreCollection;

interface HighscoreByAllRoomsLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): AllRoomsHighscoreCollection;
}
