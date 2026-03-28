<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Adapters\Outbound\Quiz\RoomByIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoomByIdLoader::class)]
final class RoomByIdLoaderTest extends TestCase
{
    private MockObject $quizRoomEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private RoomByIdLoader $roomByIdLoader;

    protected function setUp(): void
    {
        $this->quizRoomEntityRepositoryMock = $this->createMock(QuizRoomRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->roomByIdLoader = new RoomByIdLoader($this->quizRoomEntityRepositoryMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $host = new User('test', 'test', 'test', 'test', 'test', true);
        $expectedQuizRoom = new QuizRoom('test', $quiz, $host, false);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $expectedQuizRoom->getId()])
            ->willReturn($expectedQuizRoom);
        $quizRoom = $this->roomByIdLoader->load($expectedQuizRoom->getId());

        self::assertSame('test', $quizRoom->getId());
        self::assertEquals($host, $quizRoom->getHost());
        self::assertEquals(new ArrayCollection(), $quizRoom->getParticipants());
        self::assertEquals($quiz, $quizRoom->getQuiz());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz room by id.',
                    'context' => [
                        'id' => $quizRoom->getId(),
                    ],
                ],
                [
                    'message' => 'Loading quiz room by id was successful.',
                    'context' => [
                        'id' => $quizRoom->getId(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithEmptyQuizRooms(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $host = new User('test', 'test', 'test', 'test', 'test', true);
        $expectedQuizRoom = new QuizRoom('test', $quiz, $host, false);
        $this->quizRoomEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $expectedQuizRoom->getId()])
            ->willReturn(null);
        try {
            $this->roomByIdLoader->load($expectedQuizRoom->getId());
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz room by id.',
                    'context' => [
                        'id' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz room by id was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz room by id was not successful.',
                    'context' => [
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
