<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizRoomCollection;

interface RoomLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): QuizRoomCollection;
}
