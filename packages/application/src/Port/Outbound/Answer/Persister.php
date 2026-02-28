<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Answer;

use CuriosityConference\Domain\Answer\Answer;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Answer $answer): string;
}
