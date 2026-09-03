<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\AllRoomsHighscoreCollection;

interface ListHighscoreByAllRoomsHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(): AllRoomsHighscoreCollection;
}
