<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;

interface ByRoomLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $id): TriviaQuestion;
}
