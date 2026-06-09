<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\RoomLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomLoader::class)]
final class RoomLoaderTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private RoomLoader $roomLoader;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->roomLoader = new RoomLoader($this->quizRoomEntityRepositoryMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoomGroup = [
            new QuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
            ),
            new QuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
            ),
            new QuizRoom(
                'test',
                'test',
                $quiz,
                null,
                $user,
                QuizRoom::STATUS_STARTED,
            ),
        ];
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn($quizRoomGroup);
        $quizCollection = $this->roomLoader->load();

        self::assertSame('test', $quizCollection->data[0]->getId());
        self::assertSame('test', $quizCollection->data[0]->getQuiz()->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz rooms.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading quiz rooms was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willThrowException(new LoadEntityFailedException('test'));
        try {
            $this->roomLoader->load();
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz rooms.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz rooms was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz rooms was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
