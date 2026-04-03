<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetMailSender;

final class PasswordResetMailSenderMock implements PasswordResetMailSender
{
    public User $inputUser;
    public int $inputToken;

    public function send(User $user, int $token): void
    {
        $this->inputUser = $user;
        $this->inputToken = $token;
    }
}
