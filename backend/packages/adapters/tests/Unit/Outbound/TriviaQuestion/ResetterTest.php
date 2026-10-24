<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuestion\Resetter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Resetter::class)]
final class ResetterTest extends TestCase
{
    private MockObject $questionEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private Resetter $resetter;

    protected function setUp(): void
    {
        $this->questionEntityRepositoryMock = $this->createMock(TriviaQuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->resetter = new Resetter(
            $this->questionEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteByQuizIsValid(): void
    {
        $question = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$question]);
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('remove')
            ->with($question);

        $this->resetter->reset();

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting all questions.',
                    'context' => [],
                ],
                [
                    'message' => 'Deleting all questions was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteByQuizWithDeleteFailedException(): void
    {
        $question = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$question]);
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('remove')
            ->with($question)
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->resetter->reset();
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting all questions.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting all questions was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting all questions was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
