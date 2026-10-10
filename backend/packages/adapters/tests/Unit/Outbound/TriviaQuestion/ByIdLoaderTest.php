<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuestion\ByIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdLoader::class)]
final class ByIdLoaderTest extends TestCase
{
    private MockObject $questionEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdLoader $byIdLoader;

    protected function setUp(): void
    {
        $this->questionEntityRepositoryMock = $this->createMock(TriviaQuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new ByIdLoader(
            $this->questionEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $questionGroup = [
            new TriviaQuestion(
                '1',
                'test1',
                $quiz,
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                '2',
                'test2',
                $quiz,
                new ArrayCollection(),
            ),
        ];
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findBy')
            ->with(['triviaQuiz' => $quiz])
            ->willReturn($questionGroup);

        $questionCollection = $this->byIdLoader->load($quiz);

        self::assertSame('1', $questionCollection->data[0]->getId());
        self::assertEquals($quiz, $questionCollection->data[0]->getTriviaQuiz());
        self::assertSame('test1', $questionCollection->data[0]->getTitle());
        self::assertSame('2', $questionCollection->data[1]->getId());
        self::assertEquals($quiz, $questionCollection->data[1]->getTriviaQuiz());
        self::assertSame('test2', $questionCollection->data[1]->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading questions by quiz id.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading questions by quiz id was successful.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithNoQuestionsFound(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $questionGroup = [];
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['triviaQuiz' => $quiz])
            ->willReturn($questionGroup);

        $questionCollection = $this->byIdLoader->load($quiz);

        self::assertSame(0, \count($questionCollection->data));
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading questions by quiz id.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading questions by quiz id was successful.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithDatabaseError(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findBy')
            ->with(['triviaQuiz' => $quiz])
            ->willThrowException(new \Exception('test'));

        try {
            $this->byIdLoader->load($quiz);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading questions by quiz id.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading questions by quiz id was not successful.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading questions by quiz id was not successful.',
                    'context' => [
                        'quizId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
