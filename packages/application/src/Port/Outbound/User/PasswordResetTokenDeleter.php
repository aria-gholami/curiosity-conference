<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Domain\Exception\DeleteFailedException;

interface PasswordResetTokenDeleter
{
    /**
     * @throws DeleteFailedException
     */
    public function delete(PasswordReset $passwordReset): void;
}
