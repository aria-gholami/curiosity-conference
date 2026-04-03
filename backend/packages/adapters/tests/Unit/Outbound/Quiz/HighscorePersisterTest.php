<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Quiz\HighscorePersister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscorePersister::class)]
final class HighscorePersisterTest extends TestCase
{
    private EntityManagerMock $entityManagerMock;
    private LoggerMock $loggerMock;
    private HighscorePersister $highscorePersister;

    protected function setUp(): void
    {
        $this->entityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->highscorePersister = new HighscorePersister($this->entityManagerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $quizRoom = new QuizRoom(
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );

        $this->highscorePersister->persist($quizRoom, $user);

        self::assertSame('test', $this->entityManagerMock->inputPersistedObject[0]->getQuizRoom()->getId());
        self::assertSame('test', $this->entityManagerMock->inputPersistedObject[0]->getUser()->getId());
        self::assertSame(0, $this->entityManagerMock->inputPersistedObject[0]->getScore());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new highscore.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting new highscore was successful.',
                    'context' => [
                        'quizRoomId' => 'test',
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
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $quizRoom = new QuizRoom(
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );

        try {
            $this->highscorePersister->persist($quizRoom, $user);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore" was not successful.', $exception->getMessage());
        }

        self::assertSame('test', $this->entityManagerMock->inputPersistedObject[0]->getQuizRoom()->getId());
        self::assertSame('test', $this->entityManagerMock->inputPersistedObject[0]->getUser()->getId());
        self::assertSame(0, $this->entityManagerMock->inputPersistedObject[0]->getScore());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting new highscore.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting new highscore was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting new highscore was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
