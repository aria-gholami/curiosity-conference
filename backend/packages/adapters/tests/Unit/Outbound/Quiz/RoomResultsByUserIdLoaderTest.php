<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\RoomResultsByUserIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomResultsByUserIdLoader::class)]
#[UsesClass(Id::class)]
final class RoomResultsByUserIdLoaderTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private RoomResultsByUserIdLoader $roomResultsByUserIdLoader;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->roomResultsByUserIdLoader = new RoomResultsByUserIdLoader(
            $this->quizRoomEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadByUserIdIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizRoomEntityResults = [
            [
                'id' => 'test',
                'status' => QuizRoom::STATUS_STARTED,
            ],
            [
                'id' => 'test2',
                'status' => QuizRoom::STATUS_CLOSED,
            ],
        ];
        $id = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findByParticipantId')
            ->with($id->value)
            ->willReturn($expectedQuizRoomEntityResults);

        $quizRoomResults = $this->roomResultsByUserIdLoader->load($id->value);

        self::assertEquals($expectedQuizRoomEntityResults, $quizRoomResults);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz room results by participant id.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading quiz room results by participant id was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadByUserIdWithLoadEntityFailedException(): void
    {
        $id = new Id('test');
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findByParticipantId')
            ->with($id->value)
            ->willThrowException(new LoadEntityFailedException('test'));

        try {
            $this->roomResultsByUserIdLoader->load($id->value);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz room results by participant id.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz room results by participant id was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz room results by participant id was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
