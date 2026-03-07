<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenDeleter;

final class PasswordResetTokenDeleterMock implements PasswordResetTokenDeleter
{
    public PasswordReset $inputPasswordReset;

    #[\Override]
    public function delete(PasswordReset $passwordReset): void
    {
        $this->inputPasswordReset = $passwordReset;
    }
}
