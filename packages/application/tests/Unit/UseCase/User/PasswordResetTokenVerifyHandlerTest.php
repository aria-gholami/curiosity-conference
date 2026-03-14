<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\ByEmailLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PasswordResetTokenDeleterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PasswordResetTokenLoaderMock;
use CuriosityConference\Application\UseCase\User\PasswordResetTokenVerifyHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetTokenVerifyHandler::class)]
#[UsesClass(PasswordResetToken::class)]
final class PasswordResetTokenVerifyHandlerTest extends TestCase
{
    private ByEmailLoaderMock $byEmailLoaderMock;
    private PasswordResetTokenLoaderMock $passwordResetTokenLoaderMock;
    private PasswordResetTokenDeleterMock $passwordResetTokenDeleterMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenVerifyHandler $passwordResetTokenVerifyHandler;

    protected function setUp(): void
    {
        $this->byEmailLoaderMock = new ByEmailLoaderMock();
        $this->passwordResetTokenLoaderMock = new PasswordResetTokenLoaderMock();
        $this->passwordResetTokenDeleterMock = new PasswordResetTokenDeleterMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenVerifyHandler = new PasswordResetTokenVerifyHandler(
            $this->byEmailLoaderMock,
            $this->passwordResetTokenLoaderMock,
            $this->passwordResetTokenDeleterMock,
            $this->loggerMock,
        );
    }

    public function testHandleAndGetIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('+30 minutes'),
        );
        $this->passwordResetTokenLoaderMock->outputPasswordReset = $passwordReset;
        $passwordResetToken = new PasswordResetToken(
            12345678,
            'test',
        );

        $verified = $this->passwordResetTokenVerifyHandler->handleAndGet($passwordResetToken);

        self::assertTrue($verified);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals($user, $this->passwordResetTokenLoaderMock->inputUser);
        self::assertEquals($passwordResetToken, $this->passwordResetTokenLoaderMock->inputPasswordResetToken);
        self::assertEquals($passwordReset, $this->passwordResetTokenDeleterMock->inputPasswordReset);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideInvalidPasswordResetToken')]
    public function testHandleAndGetWithInvalidPasswordResetToken(array $payload): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->passwordResetTokenLoaderMock->outputPasswordReset = $payload['passwordReset'];
        $passwordResetToken = new PasswordResetToken(
            12345678,
            'test',
        );

        $verified = $this->passwordResetTokenVerifyHandler->handleAndGet($passwordResetToken);

        self::assertFalse($verified);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals($user, $this->passwordResetTokenLoaderMock->inputUser);
        self::assertEquals($passwordResetToken, $this->passwordResetTokenLoaderMock->inputPasswordResetToken);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public static function provideInvalidPasswordResetToken(): iterable
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $payload = [
            'passwordReset' => new PasswordReset(
                'test',
                $user,
                12345678,
                new \DateTimeImmutable('+30 minutes'),
            ),
        ];

        yield 'passwordReset equals null' => [\array_replace($payload, ['passwordReset' => null])];
        yield 'expired token' => [\array_replace($payload, ['passwordReset' => new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('-30 minutes'),
        ),
        ])];
    }

    public function testHandleAndGetWithPasswordResetTokenVerifyFailedException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->byEmailLoaderMock->throwException = new LoadEntityFailedException('test');
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('+30 minutes'),
        );
        $this->passwordResetTokenLoaderMock->outputPasswordReset = $passwordReset;
        $passwordResetToken = new PasswordResetToken(
            12345678,
            'test',
        );

        try {
            $this->passwordResetTokenVerifyHandler->handleAndGet($passwordResetToken);
            $this->fail('PasswordResetTokenVerifyFailedException was expected to be thrown.');
        } catch (PasswordResetTokenVerifyFailedException $exception) {
            self::assertSame('Password reset token verification failed.', $exception->getMessage());
        }

        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Password reset token verification failed.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
