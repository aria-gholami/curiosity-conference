<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface RoomParticipantPersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(TriviaQuizRoom $quizRoom): TriviaQuizRoom;
}
