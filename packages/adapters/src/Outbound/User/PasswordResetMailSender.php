<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Outbound\User\PasswordResetMailSender as PasswordResetMailSenderInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class PasswordResetMailSender implements PasswordResetMailSenderInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $adminEmail,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[\Override]
    public function send(User $user, int $token): void
    {
        $email = new TemplatedEmail()
            ->from($this->adminEmail)
            ->replyTo($user->getEmail())
            ->to($user->getEmail())
            ->subject('Curiosity App Password reset')
            ->htmlTemplate('emails/password-reset.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
