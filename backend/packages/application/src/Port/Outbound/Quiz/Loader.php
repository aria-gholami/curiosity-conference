<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizCollection;

interface Loader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): QuizCollection;
}
