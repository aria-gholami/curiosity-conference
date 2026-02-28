<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Adapters\Outbound\Quiz\ByTitleDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByTitleDeleter::class)]
#[UsesClass(Id::class)]
final class ByTitleDeleterTest extends TestCase
{
    private MockObject $quizEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByTitleDeleter $byTitleDeleter;

    protected function setUp(): void
    {
        $this->quizEntityRepositoryMock = $this->createMock(QuizRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byTitleDeleter = new ByTitleDeleter(
            $this->quizEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $quiz = new Quiz(
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

        $this->byTitleDeleter->delete($id);

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
        $quiz = new Quiz(
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

        $this->byTitleDeleter->delete($id);

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
        $quiz = new Quiz(
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
            $this->byTitleDeleter->delete($id);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz" was not successful.', $exception->getMessage());
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
