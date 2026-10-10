<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomCollection;

interface RoomLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): QuizRoomCollection;
}
