<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;

interface PasswordResetTokenVerifyHandler
{
    /**
     * @throws PasswordResetTokenVerifyFailedException
     */
    public function handleAndGet(PasswordResetToken $passwordResetToken): bool;
}
