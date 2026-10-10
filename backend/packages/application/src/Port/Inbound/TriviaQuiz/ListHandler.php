<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizCollection;

interface ListHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handle(): QuizCollection;
}
