<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User as UserEntity;
use CuriosityConference\Adapters\Outbound\User\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
#[UsesClass(UserName::class)]
#[UsesClass(PersistFailedException::class)]
final class PersisterTest extends TestCase
{
    private EntityManagerMock $entityManagerMock;
    private LoggerMock $loggerMock;
    private Persister $persister;

    protected function setUp(): void
    {
        $this->entityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->persister = new Persister($this->entityManagerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $userName = new UserName(
            'test',
        );
        $userId = $this->persister->persist($userName);

        self::assertIsString($userId);
        $expectedUserEntity = new UserEntity(
            'test',
            'test',
        );
        self::assertIsString($expectedUserEntity->getId());
        self::assertSame($expectedUserEntity->getUserName(), $this->entityManagerMock->inputPersistedObject[0]->getUserName());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting user.',
                    'context' => [],
                ],
                [
                    'message' => 'Persisting user was successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start persisting user.',
                    'context' => [
                        'userName' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting user was successful.',
                    'context' => [
                        'userName' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithThrownException(): void
    {
        $this->entityManagerMock->throwPersistException = new \Exception('test');

        $userName = new UserName(
            'test',
        );
        try {
            $this->persister->persist($userName);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        $expectedUserEntity = new UserEntity(
            'test',
            'test',
        );
        self::assertIsString($expectedUserEntity->getId());
        self::assertSame($expectedUserEntity->getUserName(), $this->entityManagerMock->inputPersistedObject[0]->getUserName());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting user.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start persisting user.',
                    'context' => [
                        'userName' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting user was not successful.',
                    'context' => [
                        'userName' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting user was not successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
