<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\ByIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdLoader::class)]
#[UsesClass(Id::class)]
final class ByIdLoaderTest extends TestCase
{
    private MockObject $quizEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdLoader $byIdLoader;

    protected function setUp(): void
    {
        $this->quizEntityRepositoryMock = $this->createMock(TriviaQuizRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new ByIdLoader(
            $this->quizEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $quiz->getId()])
            ->willReturn($quiz);

        $id = new Id($quiz->getId());
        $quiz = $this->byIdLoader->load($id);

        self::assertSame('test', $quiz->getId());
        self::assertSame('test', $quiz->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz by id.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading quiz by id was successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithNoQuizFound(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $quiz->getId()])
            ->willReturn(null);

        $id = new Id($quiz->getId());
        try {
            $this->byIdLoader->load($id);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz by id.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz by id was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz by id was not successful.',
                    'context' => [
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz" was not successful.',
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
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $quiz->getId()])
            ->willThrowException(new \Exception('test'));

        $id = new Id($quiz->getId());
        try {
            $this->byIdLoader->load($id);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz by id.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz by id was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz by id was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
