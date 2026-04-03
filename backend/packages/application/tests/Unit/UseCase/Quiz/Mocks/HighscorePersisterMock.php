<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscorePersister;

final class HighscorePersisterMock implements HighscorePersister
{
    public QuizRoom $inputQuizRoom;
    public User $inputUser;

    #[\Override]
    public function persist(QuizRoom $quizRoom, User $user): void
    {
        $this->inputQuizRoom = $quizRoom;
        $this->inputUser = $user;
    }
}
