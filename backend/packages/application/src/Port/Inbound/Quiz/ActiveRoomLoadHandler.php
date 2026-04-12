<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;

interface ActiveRoomLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $userId): ?QuizRoom;
}
