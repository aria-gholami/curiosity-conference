<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface HighscoreLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(User $user, QuizRoom $quizRoom): Highscore;
}
