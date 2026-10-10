<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface ActiveRoomLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $userId): ?TriviaQuizRoom;
}
