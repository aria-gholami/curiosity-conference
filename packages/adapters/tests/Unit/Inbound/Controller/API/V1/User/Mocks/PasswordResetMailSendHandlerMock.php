<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks;

use CuriosityConference\Application\Port\Inbound\User\PasswordResetMailSendHandler;
use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\User\Model\Email;

final class PasswordResetMailSendHandlerMock implements PasswordResetMailSendHandler
{
    public Email $inputEmail;
    public ?CouldNotSentMailException $throwException = null;

    /**
     * @throws CouldNotSentMailException
     */
    #[\Override]
    public function handle(Email $email): void
    {
        $this->inputEmail = $email;
        if ($this->throwException instanceof CouldNotSentMailException) {
            throw $this->throwException;
        }
    }
}
