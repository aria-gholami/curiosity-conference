<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface RoomByIdDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(Id $id): void;
}
