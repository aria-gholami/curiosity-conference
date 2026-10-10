<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\RoomUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomUpdater::class)]
final class RoomUpdaterTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private RoomUpdater $roomUpdater;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(TriviaQuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->roomUpdater = new RoomUpdater(
            $this->quizRoomEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testUpdateIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);

        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('flush');

        $this->roomUpdater->update($quizRoom);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating quiz room.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
                [
                    'message' => 'Updating quiz room was successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateWithUpdateFailedException(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('flush')
            ->willThrowException(new UpdateFailedException('test'));

        try {
            $this->roomUpdater->update($quizRoom);
            $this->fail('UpdateFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating quiz room.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating quiz room was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating quiz room was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
