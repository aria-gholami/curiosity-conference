<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface RoomParticipantPersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(QuizRoom $quizRoom): QuizRoom;
}
