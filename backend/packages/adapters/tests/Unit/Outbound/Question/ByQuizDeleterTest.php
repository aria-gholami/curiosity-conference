<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionRepository;
use CuriosityConference\Adapters\Outbound\Question\ByQuizDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByQuizDeleter::class)]
final class ByQuizDeleterTest extends TestCase
{
    private MockObject $questionEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByQuizDeleter $byQuizDeleter;

    protected function setUp(): void
    {
        $this->questionEntityRepositoryMock = $this->createMock(QuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byQuizDeleter = new ByQuizDeleter(
            $this->questionEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteByQuizIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $question = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['quiz' => $quiz])
            ->willReturn([$question]);
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('remove')
            ->with($question);

        $this->byQuizDeleter->delete($quiz);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting question by quiz.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting question by quiz was successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteByQuizWithDeleteFailedException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $question = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['quiz' => $quiz])
            ->willReturn([$question]);
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('remove')
            ->with($question)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byQuizDeleter->delete($quiz);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Question" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting question by quiz.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting question by quiz was not successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting question by quiz was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
