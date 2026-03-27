<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Quiz\RoomParticipantPersister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomParticipantPersister::class)]
final class RoomParticipantPersisterTest extends TestCase
{
    private EntityManagerMock $entityManagerMock;
    private LoggerMock $loggerMock;
    private RoomParticipantPersister $roomParticipantPersister;

    protected function setUp(): void
    {
        $this->entityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->roomParticipantPersister = new RoomParticipantPersister($this->entityManagerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $host = new User('test', 'test', 'test', 'test', 'test', true);
        $quizRoom = new QuizRoom('test', $quiz, $host, new ArrayCollection(), false);
        $this->roomParticipantPersister->persist($quizRoom);

        self::assertSame($quizRoom->getId(), $this->entityManagerMock->inputPersistedObject[0]->getId());
        self::assertSame($quizRoom->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertSame($quizRoom->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertSame($quizRoom->getParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getParticipants());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room participant.',
                    'context' => [
                        'quizId' => $quizRoom->getId(),
                    ],
                ],
                [
                    'message' => 'Persisting new quiz room participant was successful.',
                    'context' => [
                        'quizId' => $quizRoom->getId(),
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
            'test',
            'test',
        );
        $host = new User('test', 'test', 'test', 'test', 'test', true);
        $quizRoom = new QuizRoom('test', $quiz, $host, new ArrayCollection(), false);
        try {
            $this->roomParticipantPersister->persist($quizRoom);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertSame($quizRoom->getId(), $this->entityManagerMock->inputPersistedObject[0]->getId());
        self::assertSame($quizRoom->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertSame($quizRoom->getHost(), $this->entityManagerMock->inputPersistedObject[0]->getHost());
        self::assertSame($quizRoom->getParticipants(), $this->entityManagerMock->inputPersistedObject[0]->getParticipants());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new quiz room participant.',
                    'context' => [
                        'quizId' => $quizRoom->getId(),
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting new quiz room participant was not successful.',
                    'context' => [
                        'quizId' => $quizRoom->getId(),
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting new quiz room participant was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                        'quizId' => $quizRoom->getId(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
