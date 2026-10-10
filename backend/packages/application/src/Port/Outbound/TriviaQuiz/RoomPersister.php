<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface RoomPersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(TriviaQuiz $quiz, User $host, string $title, string $status): TriviaQuizRoom;
}
