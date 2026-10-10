<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuestion;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface ByIdDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Id $id): void;
}
