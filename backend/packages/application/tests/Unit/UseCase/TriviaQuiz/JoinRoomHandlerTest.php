<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\UserByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\RoomParticipantPersisterMock;
use CuriosityConference\Application\UseCase\TriviaQuiz\JoinRoomHandler;
use CuriosityConference\Domain\TriviaQuiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\TriviaQuiz\JoinQuizRoom;
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
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_CREATED);
        $this->roomByIdLoaderMock->outputQuizRoom = $expectedQuizRoom;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomParticipantPersisterMock->outputQuizRoom = $expectedQuizRoom;

        $joinQuizRoom = new JoinQuizRoom(
            'test',
            'test',
        );
        $quizRoom = $this->joinRoomHandler->handleAndGet($joinQuizRoom);

        self::assertEquals($expectedQuizRoom, $quizRoom);
        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame('test', $this->userByIdLoaderMock->inputId);
        self::assertSame('test', $this->roomParticipantPersisterMock->inputQuizRoom->getId());
        self::assertSame(TriviaQuizRoom::STATUS_CREATED, $this->roomParticipantPersisterMock->inputQuizRoom->getStatus());
        self::assertEquals(new ArrayCollection([$user]), $this->roomParticipantPersisterMock->inputQuizRoom->getTriviaParticipants());
    }

    public function testHandleAndGetWithQuizRoomIsAlreadyStartedException(): void
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
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomParticipantPersisterMock->outputQuizRoom = $quizRoom;

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
