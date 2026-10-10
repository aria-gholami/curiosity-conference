<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreLoader;

final class HighscoreLoaderMock implements HighscoreLoader
{
    public User $inputUser;
    public TriviaQuizRoom $inputQuizRoom;
    public TriviaHighscore $outputHighscore;

    #[\Override]
    public function load(User $user, TriviaQuizRoom $quizRoom): TriviaHighscore
    {
        $this->inputUser = $user;
        $this->inputQuizRoom = $quizRoom;

        return $this->outputHighscore;
    }
}
