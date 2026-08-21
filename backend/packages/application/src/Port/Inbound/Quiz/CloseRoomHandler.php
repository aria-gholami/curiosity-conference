<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface CloseRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    public function handle(Id $quizRoomId): void;
}
