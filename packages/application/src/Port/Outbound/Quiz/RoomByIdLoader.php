<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface RoomByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $id): QuizRoom;
}
