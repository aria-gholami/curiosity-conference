<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\QuestionCollection;

interface ByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(Quiz $quiz): QuestionCollection;
}
