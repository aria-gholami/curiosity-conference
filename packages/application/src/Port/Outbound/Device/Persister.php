<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\Device;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface Persister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(string $registrationToken, User $user): void;
}
