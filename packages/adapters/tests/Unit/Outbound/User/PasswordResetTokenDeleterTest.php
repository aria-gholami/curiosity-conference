<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Adapters\Outbound\User\PasswordResetTokenDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetTokenDeleter::class)]
final class PasswordResetTokenDeleterTest extends TestCase
{
    private MockObject $passwordResetRepositoryMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenDeleter $passwordResetTokenDeleter;

    protected function setUp(): void
    {
        $this->passwordResetRepositoryMock = $this->createMock(PasswordResetRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenDeleter = new PasswordResetTokenDeleter(
            $this->passwordResetRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('07-03-2026'),
        );
        $this->passwordResetRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($passwordReset);

        $this->passwordResetTokenDeleter->delete($passwordReset);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting password reset token data.',
                    'context' => [
                        'email' => $passwordReset->getUser()->getEmail(),
                        'token' => $passwordReset->getToken(),
                    ],
                ],
                [
                    'message' => 'Deleting password reset token data was successful.',
                    'context' => [
                        'email' => $passwordReset->getUser()->getEmail(),
                        'token' => $passwordReset->getToken(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $passwordReset = new PasswordReset(
            'test',
            $user,
            12345678,
            new \DateTimeImmutable('07-03-2026'),
        );
        $this->passwordResetRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($passwordReset)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->passwordResetTokenDeleter->delete($passwordReset);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting password reset token data.',
                    'context' => [
                        'email' => $passwordReset->getUser()->getEmail(),
                        'token' => $passwordReset->getToken(),
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting password reset token data was not successful.',
                    'context' => [
                        'email' => $passwordReset->getUser()->getEmail(),
                        'token' => $passwordReset->getToken(),
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting password reset token data was not successful.',
                    'context' => [
                        'email' => $passwordReset->getUser()->getEmail(),
                        'token' => $passwordReset->getToken(),
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
