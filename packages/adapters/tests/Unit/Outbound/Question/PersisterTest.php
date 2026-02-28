<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Question\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Question\Question;
use PHPUnit\Framework\Attributes\CoversClass;
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

    public function testPersistIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $question = new Question(
            $quiz,
            'test',
        );
        $this->persister->persist($question);

        $expectedQuizEntity = new QuestionEntity(
            'test',
            $quiz,
            'test',
        );
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertEquals($expectedQuizEntity->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertTrue($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting question.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting question was successful.',
                    'context' => [
                        'questionTitle' => 'test',
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
        $question = new Question(
            $quiz,
            'test',
        );
        try {
            $this->persister->persist($question);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Question" was not successful.', $exception->getMessage());
        }

        $expectedQuizEntity = new QuestionEntity(
            'test',
            $quiz,
            'test',
        );
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertEquals($expectedQuizEntity->getQuiz(), $this->entityManagerMock->inputPersistedObject[0]->getQuiz());
        self::assertFalse($this->entityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting question.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting question was not successful.',
                    'context' => [
                        'questionTitle' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting question was not successful.',
                    'context' => [
                        'questionTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
