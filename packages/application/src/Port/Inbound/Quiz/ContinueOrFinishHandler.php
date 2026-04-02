<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\Id;

interface ContinueOrFinishHandler
{
    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handle(Id $quizRoomId): void;
}
