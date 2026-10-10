<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaQuiz;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface RoomResultsByUserIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $userId): array;
}
