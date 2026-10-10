<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizCollection;

interface Loader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): QuizCollection;
}
