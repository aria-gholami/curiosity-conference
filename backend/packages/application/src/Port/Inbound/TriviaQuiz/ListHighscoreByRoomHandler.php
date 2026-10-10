<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface ListHighscoreByRoomHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $quizRoomId): HighscoreCollection;
}
