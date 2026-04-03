<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;
use CuriosityConference\Domain\Quiz\Id;

interface ListHighscoreByRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $quizRoomId): HighscoreCollection;
}
