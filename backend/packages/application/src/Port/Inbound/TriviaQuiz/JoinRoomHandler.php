<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\TriviaQuiz\JoinQuizRoom;

interface JoinRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     * @throws QuizRoomIsAlreadyStartedException
     */
    public function handleAndGet(JoinQuizRoom $joinQuizRoom): TriviaQuizRoom;
}
