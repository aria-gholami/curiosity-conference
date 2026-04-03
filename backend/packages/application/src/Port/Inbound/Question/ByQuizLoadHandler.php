<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Question;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Id;

interface ByQuizLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handle(Id $id): QuestionCollection;
}
