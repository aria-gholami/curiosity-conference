<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Quiz\RoomPersister;
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
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $this->quizRoomPersister->persist($quiz, $user, $title, QuizRoom::STATUS_STARTED);

        $expectedQuizEntity = new QuizRoom(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $expectedQuizEntity->addParticipant($user);
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertSame($expectedQuizEntity->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertEquals($expectedQuizEntity->getParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getParticipants());
        self::assertSame(QuizRoom::STATUS_STARTED, $this->entityManagerMock->inputPersistedObject[0]->getStatus());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room.',
                    'context' => [
                        'quizId' => 'test',
                        'quizRoomTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting new quiz room was successful.',
                    'context' => [
                        'quizId' => 'test',
                        'quizRoomTitle' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithThrownException(): void
    {
        $this->entityManagerMock->throwPersistException = new \Exception('test');
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        try {
            $this->quizRoomPersister->persist($quiz, $user, $title, QuizRoom::STATUS_STARTED);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        $expectedQuizEntity = new QuizRoom(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $expectedQuizEntity->addParticipant($user);
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertSame($expectedQuizEntity->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertEquals($expectedQuizEntity->getParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getParticipants());
        self::assertSame(QuizRoom::STATUS_STARTED, $this->entityManagerMock->inputPersistedObject[0]->getStatus());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room.',
                    'context' => [
                        'quizId' => 'test',
                        'quizRoomTitle' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting new quiz room was not successful.',
                    'context' => [
                        'quizId' => 'test',
                        'quizRoomTitle' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting new quiz room was not successful.',
                    'context' => [
                        'quizId' => 'test',
                        'quizRoomTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
