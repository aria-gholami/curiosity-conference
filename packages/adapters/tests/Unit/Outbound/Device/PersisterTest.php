<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Device;

use CuriosityConference\Adapters\Outbound\Device\Persister;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
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
        $registrationToken = 'test token 1';
        $user = new User(
            'test id 1',
            'test',
            'test',
            'test@web.de',
            'Test123!',
            false,
        );
        $this->persister->persist($registrationToken, $user);

        self::assertSame($user->getId(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getId());
        self::assertSame($registrationToken, $this->entityManagerMock->inputPersistedObject[0]->getRegistrationToken());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting device.',
                    'context' => [
                        'registrationToken' => $registrationToken,
                        'userId' => $user->getId(),
                    ],
                ],
                [
                    'message' => 'Persisting device was successful.',
                    'context' => [
                        'registrationToken' => $registrationToken,
                        'userId' => $user->getId(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithThrownException(): void
    {
        $this->entityManagerMock->throwPersistException = new \Exception('test');

        $registrationToken = 'test token 1';
        $user = new User(
            'test id 1',
            'test',
            'test',
            'test@web.de',
            'Test123!',
            false,
        );
        try {
            $this->persister->persist($registrationToken, $user);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Device" was not successful.', $exception->getMessage());
        }

        self::assertSame($user->getId(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getId());
        self::assertSame($registrationToken, $this->entityManagerMock->inputPersistedObject[0]->getRegistrationToken());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting device.',
                    'context' => [
                        'registrationToken' => $registrationToken,
                        'userId' => $user->getId(),
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting device was not successful.',
                    'context' => [
                        'registrationToken' => $registrationToken,
                        'userId' => $user->getId(),
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting device was not successful.',
                    'context' => [
                        'registrationToken' => $registrationToken,
                        'userId' => $user->getId(),
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
