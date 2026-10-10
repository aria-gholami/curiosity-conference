<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface ByQuizLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handle(Id $id): QuestionCollection;
}
