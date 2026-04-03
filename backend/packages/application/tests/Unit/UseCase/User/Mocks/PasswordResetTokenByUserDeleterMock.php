<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenByUserDeleter;

final class PasswordResetTokenByUserDeleterMock implements PasswordResetTokenByUserDeleter
{
    public User $inputUser;

    #[\Override]
    public function delete(User $user): void
    {
        $this->inputUser = $user;
    }
}
