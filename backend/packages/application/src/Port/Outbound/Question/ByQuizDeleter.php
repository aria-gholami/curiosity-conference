<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface ByQuizDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Quiz $quiz): void;
}
