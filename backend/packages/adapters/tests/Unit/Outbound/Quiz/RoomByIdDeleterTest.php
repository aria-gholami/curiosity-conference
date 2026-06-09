<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\RoomByIdDeleter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomByIdDeleter::class)]
#[UsesClass(Id::class)]
final class RoomByIdDeleterTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private RoomByIdDeleter $roomByIdDeleter;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->roomByIdDeleter = new RoomByIdDeleter(
            $this->quizRoomEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizEntity = new QuizRoom(
            'test',
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $id = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($expectedQuizEntity);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($expectedQuizEntity);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('flush');

        $this->roomByIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz room.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting quiz room was successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithNoQuizRoomFound(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizEntity = new QuizRoom(
            'test',
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $id = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn(null);
        $this->quizRoomEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($expectedQuizEntity);
        $this->quizRoomEntityRepositoryMock->expects(self::never())
            ->method('flush');

        $this->roomByIdDeleter->delete($id);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz room.',
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
        );
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizEntity = new QuizRoom(
            'test',
            'test',
            $quiz,
            null,
            $user,
            QuizRoom::STATUS_STARTED,
        );
        $id = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id->value])
            ->willReturn($expectedQuizEntity);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->willThrowException(new DeleteFailedException('test'));
        $this->quizRoomEntityRepositoryMock->expects(self::never())
            ->method('flush');

        try {
            $this->roomByIdDeleter->delete($id);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting quiz room.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting quiz room was not successful.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting quiz room was not successful.',
                    'context' => [
                        'id' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
