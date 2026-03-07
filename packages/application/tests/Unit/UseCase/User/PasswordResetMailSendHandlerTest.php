<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\ByEmailLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PasswordResetMailSenderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PasswordResetTokenPersisterMock;
use CuriosityConference\Application\UseCase\User\PasswordResetMailSendHandler;
use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetMailSendHandler::class)]
#[UsesClass(Email::class)]
final class PasswordResetMailSendHandlerTest extends TestCase
{
    private ByEmailLoaderMock $byEmailLoaderMock;
    private PasswordResetTokenPersisterMock $passwordResetTokenPersisterMock;
    private PasswordResetMailSenderMock $passwordResetMailSenderMock;
    private LoggerMock $loggerMock;
    private PasswordResetMailSendHandler $passwordResetMailSendHandler;

    protected function setUp(): void
    {
        $this->byEmailLoaderMock = new ByEmailLoaderMock();
        $this->passwordResetTokenPersisterMock = new PasswordResetTokenPersisterMock();
        $this->passwordResetMailSenderMock = new PasswordResetMailSenderMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordResetMailSendHandler = new PasswordResetMailSendHandler(
            $this->byEmailLoaderMock,
            $this->passwordResetTokenPersisterMock,
            $this->passwordResetMailSenderMock,
            $this->loggerMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $token = 12345678;
        $this->passwordResetTokenPersisterMock->outputToken = $token;

        $email = new Email('test');
        $this->passwordResetMailSendHandler->handle($email);

        self::assertSame($email->value, $this->byEmailLoaderMock->inputEmail);
        self::assertSame($user->getId(), $this->passwordResetTokenPersisterMock->inputUser->getId());
        self::assertSame($user->getEmail(), $this->passwordResetTokenPersisterMock->inputUser->getEmail());
        self::assertSame($user->getFirstName(), $this->passwordResetTokenPersisterMock->inputUser->getFirstName());
        self::assertSame($user->getLastName(), $this->passwordResetTokenPersisterMock->inputUser->getLastName());
        self::assertSame($user->getId(), $this->passwordResetMailSenderMock->inputUser->getId());
        self::assertSame($user->getEmail(), $this->passwordResetMailSenderMock->inputUser->getEmail());
        self::assertSame($user->getFirstName(), $this->passwordResetMailSenderMock->inputUser->getFirstName());
        self::assertSame($user->getLastName(), $this->passwordResetMailSenderMock->inputUser->getLastName());
        self::assertSame($token, $this->passwordResetMailSenderMock->inputToken);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testHandleWithCouldNotSentMailException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->byEmailLoaderMock->throwException = LoadEntityFailedException::loadFailed(User::class);
        $token = 12345678;
        $this->passwordResetTokenPersisterMock->outputToken = $token;

        $email = new Email('test');
        try {
            $this->passwordResetMailSendHandler->handle($email);
            $this->fail('CouldNotSentMailException was expected to be thrown.');
        } catch (CouldNotSentMailException $exception) {
            self::assertSame('Mail for "passwordReset" was not sent successfully.', $exception->getMessage());
        }

        self::assertSame($email->value, $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Password reset mail data is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
