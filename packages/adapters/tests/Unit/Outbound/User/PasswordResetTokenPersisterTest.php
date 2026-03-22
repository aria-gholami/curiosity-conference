<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\User\PasswordResetTokenPersister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetTokenPersister::class)]
#[UsesClass(PersistFailedException::class)]
final class PasswordResetTokenPersisterTest extends TestCase
{
    private EntityManagerMock $entityManagerMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenPersister $passwordResetTokenPersister;

    protected function setUp(): void
    {
        $this->entityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenPersister = new PasswordResetTokenPersister($this->entityManagerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@web.de',
            'Test123!',
            false,
        );
        $token = $this->passwordResetTokenPersister->persist($user);

        self::assertIsInt($token);
        self::assertGreaterThanOrEqual(10000000, $token);
        self::assertLessThanOrEqual(99999999, $token);
        self::assertSame($user->getFirstName(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getFirstName());
        self::assertSame($user->getLastName(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getLastName());
        self::assertSame($user->getEmail(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getEmail());
        self::assertSame($user->getPassword(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getPassword());
        self::assertSame($token, $this->entityManagerMock->inputPersistedObject[0]->getToken());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting password reset token.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting password reset token was successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithThrownException(): void
    {
        $this->entityManagerMock->throwPersistException = new \Exception('test');

        $user = new User(
            'test',
            'test',
            'test',
            'test@web.de',
            'Test123!',
            false,
        );
        try {
            $this->passwordResetTokenPersister->persist($user);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset" was not successful.', $exception->getMessage());
        }

        self::assertSame($user->getFirstName(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getFirstName());
        self::assertSame($user->getLastName(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getLastName());
        self::assertSame($user->getEmail(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getEmail());
        self::assertSame($user->getPassword(), $this->entityManagerMock->inputPersistedObject[0]->getUser()->getPassword());
        self::assertGreaterThanOrEqual(10000000, $this->entityManagerMock->inputPersistedObject[0]->getToken());
        self::assertLessThanOrEqual(99999999, $this->entityManagerMock->inputPersistedObject[0]->getToken());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting password reset token.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting password reset token was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting password reset token was not successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
