<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface ByNameLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $title): ?Quiz;
}
