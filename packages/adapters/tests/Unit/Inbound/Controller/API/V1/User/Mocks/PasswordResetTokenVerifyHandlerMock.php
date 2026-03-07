<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\PasswordResetTokenVerifyHandler;
use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;

final class PasswordResetTokenVerifyHandlerMock implements PasswordResetTokenVerifyHandler
{
    public PasswordResetToken $inputPasswordResetToken;
    public bool $outputVerified;
    public ?PasswordResetTokenVerifyFailedException $throwException = null;

    /**
     * @throws PasswordResetTokenVerifyFailedException
     */
    #[\Override]
    public function handleAndGet(PasswordResetToken $passwordResetToken): bool
    {
        $this->inputPasswordResetToken = $passwordResetToken;
        if ($this->throwException instanceof PasswordResetTokenVerifyFailedException) {
            throw $this->throwException;
        }

        return $this->outputVerified;
    }
}
