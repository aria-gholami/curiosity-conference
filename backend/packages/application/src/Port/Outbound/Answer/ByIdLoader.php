<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Answer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface ByIdLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $id): Answer;
}
