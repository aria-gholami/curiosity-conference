<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface ByQuizDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(TriviaQuiz $quiz): void;
}
