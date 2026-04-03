<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(Quiz $quiz): string;
}
