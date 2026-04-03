<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\UpdateFailedException;

interface RoomUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(QuizRoom $quizRoom): void;
}
