<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface RoomUpdater
{
    /**
     * @throws PersistFailedException
     */
    public function update(QuizRoom $quizRoom): void;
}
