<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\UserByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\RoomParticipantPersisterMock;
use CuriosityConference\Application\UseCase\Quiz\JoinRoomHandler;
use CuriosityConference\Domain\Quiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JoinRoomHandler::class)]
#[UsesClass(JoinQuizRoom::class)]
final class JoinRoomHandlerTest extends TestCase
{
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private UserByIdLoaderMock $userByIdLoaderMock;
    private RoomParticipantPersisterMock $roomParticipantPersisterMock;
    private JoinRoomHandler $joinRoomHandler;

    protected function setUp(): void
    {
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->userByIdLoaderMock = new UserByIdLoaderMock();
        $this->roomParticipantPersisterMock = new RoomParticipantPersisterMock();
        $this->joinRoomHandler = new JoinRoomHandler(
            $this->roomByIdLoaderMock,
            $this->userByIdLoaderMock,
            $this->roomParticipantPersisterMock,
        );
    }

    public function testHandleAndGetIsValid(): void
    {
        $quiz = new Quiz(
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
        $quizRoom = new QuizRoom('test', $quiz, null, $user, QuizRoom::STATUS_CREATED);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomParticipantPersisterMock->outputQuizRoomId = 'test';

        $joinQuizRoom = new JoinQuizRoom(
            'test',
            'test',
        );
        $quizRoomId = $this->joinRoomHandler->handleAndGet($joinQuizRoom);

        self::assertSame('test', $quizRoomId);
        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame('test', $this->userByIdLoaderMock->inputId);
        self::assertSame('test', $this->roomParticipantPersisterMock->inputQuizRoom->getId());
        self::assertSame(QuizRoom::STATUS_CREATED, $this->roomParticipantPersisterMock->inputQuizRoom->getStatus());
        self::assertEquals(new ArrayCollection([$user]), $this->roomParticipantPersisterMock->inputQuizRoom->getParticipants());
    }

    public function testHandleAndGetWithQuizRoomIsAlreadyStartedException(): void
    {
        $quiz = new Quiz(
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
        $quizRoom = new QuizRoom('test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomParticipantPersisterMock->outputQuizRoomId = 'test';

        $joinQuizRoom = new JoinQuizRoom(
            'test',
            'test',
        );
        try {
            $this->joinRoomHandler->handleAndGet($joinQuizRoom);
            $this->fail('QuizRoomIsAlreadyStartedException was expected to be thrown.');
        } catch (QuizRoomIsAlreadyStartedException $exception) {
            self::assertSame('Quiz room is already started.', $exception->getMessage());
        }

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
    }
}
