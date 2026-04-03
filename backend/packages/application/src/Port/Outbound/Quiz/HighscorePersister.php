<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface HighscorePersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(QuizRoom $quizRoom, User $user): void;
}
