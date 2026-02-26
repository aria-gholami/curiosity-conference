<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;

interface CreateHandler
{
    /**
     * @throws PersistFailedException
     */
    public function handle(Quiz $quiz): void;
}
