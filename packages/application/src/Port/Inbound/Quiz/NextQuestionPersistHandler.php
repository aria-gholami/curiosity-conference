<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;

interface NextQuestionPersistHandler
{
    /**
     * @throws CurrentQuestionNotFoundException
     * @throws LoadEntityFailedException
     * @throws PersistFailedException
     */
    public function handle(string $quizRoomId): void;
}
