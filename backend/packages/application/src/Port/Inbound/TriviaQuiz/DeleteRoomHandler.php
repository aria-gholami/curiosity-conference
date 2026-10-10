<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface DeleteRoomHandler
{
    /**
     * @throws DeleteFailedException
     */
    public function handle(Id $quizRoomId): void;
}
