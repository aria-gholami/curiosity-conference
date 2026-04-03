<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizRoomCollection;

interface ListRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(): QuizRoomCollection;
}
