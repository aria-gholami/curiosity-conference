<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\ByEmailDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByEmailDeleter::class)]
#[UsesClass(Email::class)]
final class ByEmailDeleterTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByEmailDeleter $byEmailDeleter;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byEmailDeleter = new ByEmailDeleter(
            $this->userEntityRepositoryMock,
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
        $email = new Email('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => $email->value])
            ->willReturn($user);
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($user);

        $this->byEmailDeleter->delete($email);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [],
                ],
                [
                    'message' => 'Deleting user was successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [
                        'email' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting user was successful.',
                    'context' => [
                        'email' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithNoUserFound(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $email = new Email('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => $email->value])
            ->willReturn(null);
        $this->userEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($user);

        $this->byEmailDeleter->delete($email);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [
                        'email' => 'test',
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
        $email = new Email('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => $email->value])
            ->willReturn($user);
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($user)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byEmailDeleter->delete($email);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting user was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start deleting user.',
                    'context' => [
                        'email' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting user was not successful.',
                    'context' => [
                        'email' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
