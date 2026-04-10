<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\ByIdDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdDeleter::class)]
#[UsesClass(Id::class)]
final class ByIdDeleterTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdDeleter $byUserNameDeleter;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byUserNameDeleter = new ByIdDeleter(
            $this->userEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $userId = new Id('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $userId->value])
            ->willReturn($user);
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($user);

        $this->byUserNameDeleter->delete($userId);

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
                        'userId' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting user was successful.',
                    'context' => [
                        'userId' => 'test',
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
        );
        $userId = new Id('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $userId->value])
            ->willReturn(null);
        $this->userEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($user);

        $this->byUserNameDeleter->delete($userId);

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
                        'userId' => 'test',
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
        );
        $userId = new Id('test');
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $userId->value])
            ->willReturn($user);
        $this->userEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($user)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byUserNameDeleter->delete($userId);
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
                        'userId' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting user was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
