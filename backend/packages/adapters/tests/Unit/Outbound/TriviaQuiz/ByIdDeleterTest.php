<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\ByIdDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdDeleter::class)]
#[UsesClass(Id::class)]
final class ByIdDeleterTest extends TestCase
{
    private MockObject $quizEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdDeleter $byIdDeleter;

    protected function setUp(): void
    {
        $this->quizEntityRepositoryMock = $this->createMock(TriviaQuizRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdDeleter = new ByIdDeleter(
            $this->quizEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
            'test',
            'test',
        );
        $id = new Id('test');
        $this->quizEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($quiz);
        $this->quizEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($quiz);

        $this->byIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting quiz was successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithNoQuizFound(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
            'test',
            'test',
        );
        $id = new Id('test');
        $this->quizEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn(null);
        $this->quizEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($quiz);

        $this->byIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
            'test',
            'test',
        );
        $id = new Id('test');
        $this->quizEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($quiz);
        $this->quizEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($quiz)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byIdDeleter->delete($id);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting quiz was not successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting quiz was not successful.',
                    'context' => [
                        'id' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
