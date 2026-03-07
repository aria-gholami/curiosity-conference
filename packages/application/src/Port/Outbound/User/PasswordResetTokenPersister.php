<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\PersistFailedException;

interface PasswordResetTokenPersister
{
    /**
     * @throws PersistFailedException
     */
    public function persist(User $user): int;
}
