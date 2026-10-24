<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\RoomPersister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomPersister::class)]
final class RoomPersisterTest extends TestCase
{
    private EntityManagerMock $entityManagerMock;
    private LoggerMock $loggerMock;
    private RoomPersister $quizRoomPersister;

    protected function setUp(): void
    {
        $this->entityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->quizRoomPersister = new RoomPersister($this->entityManagerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $this->quizRoomPersister->persist($user, $title, TriviaQuizRoom::STATUS_STARTED);

        $expectedQuizEntity = new TriviaQuizRoom(
            'test',
            $title,
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $expectedQuizEntity->addTriviaParticipant($user);
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertEquals($expectedQuizEntity->getTriviaParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaParticipants());
        self::assertSame(TriviaQuizRoom::STATUS_STARTED, $this->entityManagerMock->inputPersistedObject[0]->getStatus());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room.',
                    'context' => [
                        'quizRoomTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting new quiz room was successful.',
                    'context' => [
                        'quizRoomTitle' => 'test',
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
        );
        $title = 'test';
        try {
            $this->quizRoomPersister->persist($user, $title, TriviaQuizRoom::STATUS_STARTED);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom" was not successful.', $exception->getMessage());
        }

        $expectedQuizEntity = new TriviaQuizRoom(
            'test',
            $title,
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $expectedQuizEntity->addTriviaParticipant($user);
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertEquals($expectedQuizEntity->getTriviaParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaParticipants());
        self::assertSame(TriviaQuizRoom::STATUS_STARTED, $this->entityManagerMock->inputPersistedObject[0]->getStatus());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room.',
                    'context' => [
                        'quizRoomTitle' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting new quiz room was not successful.',
                    'context' => [
                        'quizRoomTitle' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting new quiz room was not successful.',
                    'context' => [
                        'quizRoomTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
