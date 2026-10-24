<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;

interface Loader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): QuestionCollection;
}
