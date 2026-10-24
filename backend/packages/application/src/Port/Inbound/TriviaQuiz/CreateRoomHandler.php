<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom as QuizRoomEntity;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoom;

interface CreateRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handleAndGet(QuizRoom $quizRoom): QuizRoomEntity;
}
