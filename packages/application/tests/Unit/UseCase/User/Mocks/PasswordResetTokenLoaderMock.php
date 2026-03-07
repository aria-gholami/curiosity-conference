<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetTokenLoader;
use CuriosityConference\Domain\User\Model\PasswordResetToken;

final class PasswordResetTokenLoaderMock implements PasswordResetTokenLoader
{
    public User $inputUser;
    public PasswordResetToken $inputPasswordResetToken;
    public ?PasswordReset $outputPasswordReset;

    #[\Override]
    public function load(User $user, PasswordResetToken $passwordResetToken): ?PasswordReset
    {
        $this->inputUser = $user;
        $this->inputPasswordResetToken = $passwordResetToken;

        return $this->outputPasswordReset;
    }
}
