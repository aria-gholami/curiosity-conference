<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface HighscoreLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(TriviaQuizRoom $quizRoom): TriviaHighscore;
}
