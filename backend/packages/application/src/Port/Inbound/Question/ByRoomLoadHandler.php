<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;

interface ByRoomLoadHandler
{
    /**
     * @throws LoadEntityFailedException
     */
    public function handleAndGet(Id $id): Question;
}
