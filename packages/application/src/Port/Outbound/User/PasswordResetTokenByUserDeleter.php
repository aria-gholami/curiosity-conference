<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface PasswordResetTokenByUserDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(User $user): void;
}
