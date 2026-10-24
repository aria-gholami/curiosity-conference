<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion as QuestionEntity;
use CuriosityConference\Adapters\Outbound\TriviaQuestion\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\Question;
use Doctrine\Common\Collections\ArrayCollection;
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
        $question = new Question(
            'test',
            [
                'test1',
                'test2',
                'test3',
                'test4',
            ],
            $correctAnswerData['correctAnswerIndex'],
        );
        $this->persister->persist($question);

        $expectedQuizEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test1',
            0 === $correctAnswerData['correctAnswerIndex'],
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test2',
            1 === $correctAnswerData['correctAnswerIndex'],
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test3',
            2 === $correctAnswerData['correctAnswerIndex'],
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test4',
            3 === $correctAnswerData['correctAnswerIndex'],
        ));
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->getTitle());
        self::assertEquals($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->getTriviaQuestion()->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->getTriviaQuestion()->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->isCorrect(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->isCorrect());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(1)->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(1)->getTitle());
        self::assertEquals($expectedQuizEntity->getTriviaAnswerCollection()->get(1)->getTriviaQuestion()->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(1)->getTriviaQuestion()->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(1)->isCorrect(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(1)->isCorrect());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(2)->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(2)->getTitle());
        self::assertEquals($expectedQuizEntity->getTriviaAnswerCollection()->get(2)->getTriviaQuestion()->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(2)->getTriviaQuestion()->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(2)->isCorrect(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(2)->isCorrect());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(3)->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(3)->getTitle());
        self::assertEquals($expectedQuizEntity->getTriviaAnswerCollection()->get(3)->getTriviaQuestion()->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(3)->getTriviaQuestion()->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(3)->isCorrect(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(3)->isCorrect());
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

        $question = new Question(
            'test',
            [
                'test',
                'test',
                'test',
                'test',
            ],
            0,
        );
        try {
            $this->persister->persist($question);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
        }

        $expectedQuizEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test',
            true,
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test',
            false,
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test',
            false,
        ));
        $expectedQuizEntity->addTriviaAnswer(new TriviaAnswer(
            'test',
            'test',
            false,
        ));
        self::assertIsString($expectedQuizEntity->getId());
        self::assertSame($expectedQuizEntity->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->getTitle());
        self::assertEquals($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->getTriviaQuestion()->getTitle(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->getTriviaQuestion()->getTitle());
        self::assertSame($expectedQuizEntity->getTriviaAnswerCollection()->get(0)->isCorrect(), $this->entityManagerMock->inputPersistedObject[0]->getTriviaAnswerCollection()->get(0)->isCorrect());
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
