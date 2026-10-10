<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuiz\Quiz;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Quiz $quiz): string;
}
