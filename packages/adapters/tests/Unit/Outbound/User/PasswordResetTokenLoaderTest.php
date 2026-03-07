<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Adapters\Outbound\User\PasswordResetTokenLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetTokenLoader::class)]
#[UsesClass(PasswordResetToken::class)]
final class PasswordResetTokenLoaderTest extends TestCase
{
    private MockObject $passwordResetEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenLoader $passwordResetTokenLoader;

    protected function setUp(): void
    {
        $this->passwordResetEntityRepositoryMock = $this->createMock(PasswordResetRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenLoader = new PasswordResetTokenLoader($this->passwordResetEntityRepositoryMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $passwordResetToken = new PasswordResetToken(
            12345678,
            'test',
        );
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('07-03-2026'),
        );
        $this->passwordResetEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'user' => $user,
                'token' => $passwordResetToken->token,
            ])
            ->willReturn($passwordReset);

        $passwordResetResult = $this->passwordResetTokenLoader->load($user, $passwordResetToken);

        self::assertSame($user->getFirstName(), $passwordResetResult->getUser()->getFirstName());
        self::assertSame($user->getLastName(), $passwordResetResult->getUser()->getLastName());
        self::assertSame($user->getEmail(), $passwordResetResult->getUser()->getEmail());
        self::assertSame($user->getPassword(), $passwordResetResult->getUser()->getPassword());
        self::assertGreaterThanOrEqual(10000000, $passwordResetResult->getToken());
        self::assertLessThanOrEqual(99999999, $passwordResetResult->getToken());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading password reset token data.',
                    'context' => [
                        'userId' => 'test',
                        'token' => 12345678,
                    ],
                ],
                [
                    'message' => 'Loading password reset token data was successful.',
                    'context' => [
                        'userId' => 'test',
                        'token' => 12345678,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $passwordResetToken = new PasswordResetToken(
            12345678,
            'test',
        );
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('07-03-2026'),
        );
        $this->passwordResetEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with([
                'user' => $user,
                'token' => $passwordResetToken->token,
            ])
            ->willThrowException(new LoadEntityFailedException('test'));

        try {
            $this->passwordResetTokenLoader->load($user, $passwordResetToken);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading password reset token data.',
                    'context' => [
                        'userId' => 'test',
                        'token' => 12345678,
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading password reset token data was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'token' => 12345678,
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading password reset token data was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                        'userId' => 'test',
                        'token' => 12345678,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
