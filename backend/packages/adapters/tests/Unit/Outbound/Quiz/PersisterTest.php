<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz as QuizEntity;
use CuriosityConference\Adapters\Outbound\Quiz\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
#[UsesClass(Quiz::class)]
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
        $quiz = new Quiz(
            'test',
            'test',
            'test',
        );
        $this->persister->persist($quiz);

        $expectedQuizEntity = new QuizEntity(
            'test',
            'test',
        );
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting quiz.',
                    'context' => [],
                ],
                [
                    'message' => 'Persisting quiz was successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start persisting quiz.',
                    'context' => [
                        'quizTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting quiz was successful.',
                    'context' => [
                        'quizTitle' => 'test',
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
        );
        try {
            $this->persister->persist($quiz);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz" was not successful.', $exception->getMessage());
        }

        $expectedQuizEntity = new QuizEntity(
            'test',
            'test',
        );
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting quiz.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start persisting quiz.',
                    'context' => [
                        'quizTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting quiz was not successful.',
                    'context' => [
                        'quizTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting quiz was not successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
