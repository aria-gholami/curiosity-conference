<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Answer;

use CuriosityConference\Adapters\Outbound\Answer\Persister;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Answer\Answer;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
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
        $this->persister = new Persister(
            $this->entityManagerMock,
            $this->loggerMock,
        );
    }

    #[DataProvider('provideDifferentAnswerData')]
    public function testPersistIsValid(array $correctAnswerData): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $question = new Question(
            'test',
            $quiz,
            'test',
        );
        $answer = new Answer(
            $question,
            [
                'testAnswer1',
                'testAnswer2',
                'testAnswer3',
                'testAnswer4',
            ],
            $correctAnswerData['correctAnswerIndex'],
        );
        $this->persister->persist($answer);

        self::assertSame('testAnswer1', $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertSame(0 === $correctAnswerData['correctAnswerIndex'], $this->entityManagerMock->inputPersistedObject[0]->isCorrect());
        self::assertSame('testAnswer2', $this->entityManagerMock->inputPersistedObject[1]->getTitle());
        self::assertSame(1 === $correctAnswerData['correctAnswerIndex'], $this->entityManagerMock->inputPersistedObject[1]->isCorrect());
        self::assertSame('testAnswer3', $this->entityManagerMock->inputPersistedObject[2]->getTitle());
        self::assertSame(2 === $correctAnswerData['correctAnswerIndex'], $this->entityManagerMock->inputPersistedObject[2]->isCorrect());
        self::assertSame('testAnswer4', $this->entityManagerMock->inputPersistedObject[3]->getTitle());
        self::assertSame(3 === $correctAnswerData['correctAnswerIndex'], $this->entityManagerMock->inputPersistedObject[3]->isCorrect());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting answers.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting answers was successful.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public static function provideDifferentAnswerData(): iterable
    {
        $correctAnswerData = [
            'correctAnswerIndex' => 0,
        ];

        yield 'correctAnswerIndex 1' => [\array_replace($correctAnswerData, ['correctAnswerIndex' => 0])];
        yield 'correctAnswerIndex 2' => [\array_replace($correctAnswerData, ['correctAnswerIndex' => 1])];
        yield 'correctAnswerIndex 3' => [\array_replace($correctAnswerData, ['correctAnswerIndex' => 2])];
        yield 'correctAnswerIndex 4' => [\array_replace($correctAnswerData, ['correctAnswerIndex' => 3])];
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
        $question = new Question(
            'test',
            $quiz,
            'test',
        );
        $answer = new Answer(
            $question,
            [
                'test',
                'test',
                'test',
                'test',
            ],
            0,
        );
        try {
            $this->persister->persist($answer);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer" was not successful.', $exception->getMessage());
        }

        self::assertSame('test', $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertTrue($this->entityManagerMock->inputPersistedObject[0]->isCorrect());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting answers.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting answers was not successful.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting answers was not successful.',
                    'context' => [
                        'questionTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
