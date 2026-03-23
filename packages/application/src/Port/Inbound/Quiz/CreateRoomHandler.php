<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\QuizRoom;

interface CreateRoomHandler
{
    /**
     * @throws EmptyStringException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handleAndGet(QuizRoom $quizRoom): string;
}
