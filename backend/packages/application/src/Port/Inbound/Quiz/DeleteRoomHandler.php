<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface DeleteRoomHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(Id $quizRoomId): void;
}
