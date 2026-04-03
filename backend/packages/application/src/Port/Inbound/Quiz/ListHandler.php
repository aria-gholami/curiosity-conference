<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizCollection;

interface ListHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handle(): QuizCollection;
}
