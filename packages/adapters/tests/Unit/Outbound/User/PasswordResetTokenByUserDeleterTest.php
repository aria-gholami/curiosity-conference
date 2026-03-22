<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\PasswordResetRepository;
use CuriosityConference\Adapters\Outbound\User\PasswordResetTokenByUserDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetTokenByUserDeleter::class)]
final class PasswordResetTokenByUserDeleterTest extends TestCase
{
    private MockObject $passwordResetRepositoryMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenByUserDeleter $passwordResetTokenByUserDeleter;

    protected function setUp(): void
    {
        $this->passwordResetRepositoryMock = $this->createMock(PasswordResetRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenByUserDeleter = new PasswordResetTokenByUserDeleter(
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
            false,
        );
        $this->passwordResetRepositoryMock->expects(self::once())
            ->method('removeByUser')
            ->with($user);

        $this->passwordResetTokenByUserDeleter->delete($user);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting all password reset token data.',
                    'context' => [
                        'email' => $user->getEmail(),
                    ],
                ],
                [
                    'message' => 'Deleting all password reset token data was successful.',
                    'context' => [
                        'email' => $user->getEmail(),
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
            false,
        );
        $this->passwordResetRepositoryMock->expects(self::once())
            ->method('removeByUser')
            ->with($user)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->passwordResetTokenByUserDeleter->delete($user);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting all password reset token data.',
                    'context' => [
                        'email' => $user->getEmail(),
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting all password reset token data was not successful.',
                    'context' => [
                        'email' => $user->getEmail(),
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting all password reset token data was not successful.',
                    'context' => [
                        'email' => $user->getEmail(),
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
