<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\TriviaAnswer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface ByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $id): TriviaAnswer;
}
