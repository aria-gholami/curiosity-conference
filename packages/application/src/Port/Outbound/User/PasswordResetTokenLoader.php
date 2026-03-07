<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;

interface PasswordResetTokenLoader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(User $user, PasswordResetToken $passwordResetToken): ?PasswordReset;
}
