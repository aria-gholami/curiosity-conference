<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Question $question): void;
}
