<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\Quiz;

interface CreateHandler
{
    /**
     * @throws PersistFailedException
     */
    public function handle(Quiz $quiz): string;
}
