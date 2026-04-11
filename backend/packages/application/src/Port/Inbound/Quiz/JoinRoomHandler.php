<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;

interface JoinRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws QuizRoomIsAlreadyStartedException
     */
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): QuizRoom;
}
