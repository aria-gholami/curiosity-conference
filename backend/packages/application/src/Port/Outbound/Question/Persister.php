<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Question\Question;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Question $question): QuestionEntity;
}
