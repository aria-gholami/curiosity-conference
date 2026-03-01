<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionRepository;
use CuriosityConference\Adapters\Outbound\Question\ByIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
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
        $this->questionEntityRepositoryMock = $this->createMock(QuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new ByIdLoader(
            $this->questionEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $questionGroup = [
            new Question(
                '1',
                $quiz,
                'test1',
            ),
            new Question(
                '2',
                $quiz,
                'test2',
            ),
        ];
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['quiz' => $quiz])
            ->willReturn($questionGroup);

        $questionCollection = $this->byIdLoader->load($quiz);

        self::assertSame('1', $questionCollection->data[0]->getId());
        self::assertEquals($quiz, $questionCollection->data[0]->getQuiz());
        self::assertSame('test1', $questionCollection->data[0]->getTitle());
        self::assertSame('2', $questionCollection->data[1]->getId());
        self::assertEquals($quiz, $questionCollection->data[1]->getQuiz());
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
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $questionGroup = [];
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['quiz' => $quiz])
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
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $questionGroup = [
            new Question(
                '1',
                $quiz,
                'test1',
            ),
            new Question(
                '2',
                $quiz,
                'test2',
            ),
        ];
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['quiz' => $quiz])
            ->willThrowException(new \Exception('test'));

        try {
            $this->byIdLoader->load($quiz);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Question" was not successful.', $exception->getMessage());
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
