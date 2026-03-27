<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\QuizRoomUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizRoomUpdater::class)]
#[UsesClass(Id::class)]
final class QuizRoomUpdaterTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private QuizRoomUpdater $quizRoomUpdater;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->quizRoomUpdater = new QuizRoomUpdater($this->quizRoomEntityRepositoryMock, $this->loggerMock);
    }

    public function testUpdateIsValid(): void
    {
        $quizId = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('updateIsStarted')
            ->with($quizId);
        $this->quizRoomUpdater->updateIsStartedToTrue($quizId);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating isStarted.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
                [
                    'message' => 'Updating isStarted was successful.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateWithUpdateFailedException(): void
    {
        $quizId = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('updateIsStarted')
            ->willThrowException(new UpdateFailedException('test'));
        try {
            $this->quizRoomUpdater->updateIsStartedToTrue($quizId);
            $this->fail('UpdateFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating isStarted.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating isStarted was not successful.',
                    'context' => [
                        'quizId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating isStarted was not successful.',
                    'context' => [
                        'quizId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
