<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface HighscorePersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(TriviaQuizRoom $quizRoom): void;
}
