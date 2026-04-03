<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenPersister;

final class PasswordResetTokenPersisterMock implements PasswordResetTokenPersister
{
    public User $inputUser;
    public int $outputToken;

    #[\Override]
    public function persist(User $user): int
    {
        $this->inputUser = $user;

        return $this->outputToken;
    }
}
