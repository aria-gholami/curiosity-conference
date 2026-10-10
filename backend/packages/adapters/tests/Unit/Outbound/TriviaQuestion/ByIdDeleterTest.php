<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuestion\ByIdDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdDeleter::class)]
#[UsesClass(Id::class)]
final class ByIdDeleterTest extends TestCase
{
    private MockObject $questionEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdDeleter $byIdDeleter;

    protected function setUp(): void
    {
        $this->questionEntityRepositoryMock = $this->createMock(TriviaQuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdDeleter = new ByIdDeleter(
            $this->questionEntityRepositoryMock,
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
        $question = new TriviaQuestion(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $id = new Id('test');
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($question);
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($question);

        $this->byIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting question.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting question was successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithNoQuestionFound(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
            'test',
            'test',
        );
        $question = new TriviaQuestion(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $id = new Id('test');
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn(null);
        $this->questionEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($question);

        $this->byIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting question.',
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
        $question = new TriviaQuestion(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $id = new Id('test');
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($question);
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($question)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byIdDeleter->delete($id);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting question.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting question was not successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting question was not successful.',
                    'context' => [
                        'id' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
