<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

interface PasswordResetMailSender
{
    /**
     * @throws TransportExceptionInterface
     */
    public function send(User $user, int $token): void;
}
