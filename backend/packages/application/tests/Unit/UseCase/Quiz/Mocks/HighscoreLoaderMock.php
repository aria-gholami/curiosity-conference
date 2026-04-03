<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader;

final class HighscoreLoaderMock implements HighscoreLoader
{
    public User $inputUser;
    public QuizRoom $inputQuizRoom;
    public Highscore $outputHighscore;

    #[\Override]
    public function load(User $user, QuizRoom $quizRoom): Highscore
    {
        $this->inputUser = $user;
        $this->inputQuizRoom = $quizRoom;

        return $this->outputHighscore;
    }
}
