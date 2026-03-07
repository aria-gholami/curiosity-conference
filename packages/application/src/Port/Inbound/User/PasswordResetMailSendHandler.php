<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Inbound\User;

use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\User\Model\Email;

interface PasswordResetMailSendHandler
{
    /**
     * @throws CouldNotSentMailException
     */
    public function handle(Email $email): void;
}
