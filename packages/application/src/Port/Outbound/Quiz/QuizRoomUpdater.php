<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface QuizRoomUpdater
{
    /**
     * @throws UpdateFailedException
     */
    public function update(Id $quizRoomId, Question $currentQuestion, string $status): void;
}
