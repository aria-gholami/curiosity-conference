<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User as UserEntity;
use CuriosityConference\Adapters\Outbound\User\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserRegistration;
use CuriosityConference\Domain\User\Model\UserWithHashedPassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
#[UsesClass(UserRegistration::class)]
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
        $user = new UserWithHashedPassword(
            'test',
            'test',
            'test@web.de',
            'Test123!',
        );
        $userId = $this->persister->persist($user);

        self::assertIsString($userId);
        $expectedUserEntity = new UserEntity(
            'test',
            'test',
            'test',
            'test@web.de',
            'Test123!',
        );
        self::assertIsString($expectedUserEntity->getId());
        self::assertSame($expectedUserEntity->getFirstName(), $this->entityManagerMock->inputPersistedObject[0]->getFirstName());
        self::assertSame($expectedUserEntity->getLastName(), $this->entityManagerMock->inputPersistedObject[0]->getLastName());
        self::assertSame($expectedUserEntity->getEmail(), $this->entityManagerMock->inputPersistedObject[0]->getEmail());
        self::assertSame($expectedUserEntity->getPassword(), $this->entityManagerMock->inputPersistedObject[0]->getPassword());
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
                        'userFirstName' => 'test',
                        'userLastName' => 'test',
                        'userEmail' => 'test@web.de',
                    ],
                ],
                [
                    'message' => 'Persisting user was successful.',
                    'context' => [
                        'userFirstName' => 'test',
                        'userLastName' => 'test',
                        'userEmail' => 'test@web.de',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithThrownException(): void
    {
        $this->entityManagerMock->throwPersistException = new \Exception('test');

        $user = new UserWithHashedPassword(
            'test',
            'test',
            'test@web.de',
            'Test123!',
        );
        try {
            $this->persister->persist($user);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        $expectedUserEntity = new UserEntity(
            'test',
            'test',
            'test',
            'test@web.de',
            'Test123!',
        );
        self::assertIsString($expectedUserEntity->getId());
        self::assertSame($expectedUserEntity->getFirstName(), $this->entityManagerMock->inputPersistedObject[0]->getFirstName());
        self::assertSame($expectedUserEntity->getLastName(), $this->entityManagerMock->inputPersistedObject[0]->getLastName());
        self::assertSame($expectedUserEntity->getEmail(), $this->entityManagerMock->inputPersistedObject[0]->getEmail());
        self::assertSame($expectedUserEntity->getPassword(), $this->entityManagerMock->inputPersistedObject[0]->getPassword());
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
                        'userFirstName' => 'test',
                        'userLastName' => 'test',
                        'userEmail' => 'test@web.de',
                    ],
                ],
                [
                    'message' => 'Persisting user was not successful.',
                    'context' => [
                        'userFirstName' => 'test',
                        'userLastName' => 'test',
                        'userEmail' => 'test@web.de',
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
