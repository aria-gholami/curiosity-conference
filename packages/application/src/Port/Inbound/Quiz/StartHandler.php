<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface StartHandler
{
    /**
     * @throws UpdateFailedException
     */
    public function Handle(Id $quizRoomId): void;
}