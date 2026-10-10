<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\UpdateFailedException;

interface RoomUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(TriviaQuizRoom $quizRoom): void;
}
