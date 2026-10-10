<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;

interface ByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(TriviaQuiz $quiz): QuestionCollection;
}
