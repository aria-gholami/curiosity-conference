<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreCollection;

interface HighscoreCollectionByQuizRoomLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(TriviaQuizRoom $quizRoom): HighscoreCollection;
}
