<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion as QuestionEntity;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\Question;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Question $question): QuestionEntity;
}
