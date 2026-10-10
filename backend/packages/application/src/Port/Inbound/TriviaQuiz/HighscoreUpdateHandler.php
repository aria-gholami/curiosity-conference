<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateResponse;

interface HighscoreUpdateHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    public function handleAndGet(HighscoreUpdateRequestBody $highscoreUpdateRequestBody): HighscoreUpdateResponse;
}
