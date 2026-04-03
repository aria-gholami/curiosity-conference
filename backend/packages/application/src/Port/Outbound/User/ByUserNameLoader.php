<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;

interface ByUserNameLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(string $userName): User;
}
