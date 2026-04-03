<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\Quiz\HighscoreUpdateResponse;

interface HighscoreUpdateHandler
{
    /**
     * @throws LoadEntityFailedException
     * @throws UpdateFailedException
     */
    public function handleAndGet(HighscoreUpdateRequestBody $highscoreUpdateRequestBody): HighscoreUpdateResponse;
}
