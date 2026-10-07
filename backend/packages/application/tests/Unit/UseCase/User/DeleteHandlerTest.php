<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\RoomResultsByUserIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\UserByIdDeleterMock;
use CuriosityConference\Application\UseCase\User\DeleteHandler;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeleteHandler::class)]
#[UsesClass(Id::class)]
final class DeleteHandlerTest extends TestCase
{
    private RoomResultsByUserIdLoaderMock $roomByUserIdLoaderMock;
    private UserByIdDeleterMock $userByIdDeleterMock;
    private HubMock $hubMock;
    private DeleteHandler $deleteHandler;

    protected function setUp(): void
    {
        $this->roomByUserIdLoaderMock = new RoomResultsByUserIdLoaderMock();
        $this->userByIdDeleterMock = new UserByIdDeleterMock();
        $this->hubMock = new HubMock();
        $this->deleteHandler = new DeleteHandler(
            $this->roomByUserIdLoaderMock,
            $this->userByIdDeleterMock,
            $this->hubMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $quizRoomResults = [
            [
                'id' => 'test2',
                'status' => QuizRoom::STATUS_CLOSED,
            ],
            [
                'id' => 'test',
                'status' => QuizRoom::STATUS_STARTED,
            ],
        ];
        $this->roomByUserIdLoaderMock->outputQuizRoomResults = $quizRoomResults;
        $this->hubMock->outputUpdate = 'test';

        $userId = new Id('test');
        $this->deleteHandler->handle($userId);

        self::assertSame($userId->value, $this->roomByUserIdLoaderMock->inputUserId);
        self::assertSame($userId->value, $this->userByIdDeleterMock->inputUserId->value);
        self::assertSame('quiz/room/list', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('quiz/room/test/participants', $this->hubMock->inputUpdateGroup[1]->getTopics()[0]);
        self::assertSame('{"action":"remove","quizRoomResults":["test2","test"]}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertSame('{"action":"remove","participantId":"test"}', $this->hubMock->inputUpdateGroup[1]->getData());
    }

    public function testHandleWithOnlyClosedRooms(): void
    {
        $quizRoomResults = [
            [
                'id' => 'test2',
                'status' => QuizRoom::STATUS_CLOSED,
            ],
            [
                'id' => 'test',
                'status' => QuizRoom::STATUS_CLOSED,
            ],
        ];
        $this->roomByUserIdLoaderMock->outputQuizRoomResults = $quizRoomResults;
        $this->hubMock->outputUpdate = 'test';

        $userId = new Id('test');
        $this->deleteHandler->handle($userId);

        self::assertSame($userId->value, $this->roomByUserIdLoaderMock->inputUserId);
        self::assertSame($userId->value, $this->userByIdDeleterMock->inputUserId->value);
        self::assertSame('quiz/room/list', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"action":"remove","quizRoomResults":["test2","test"]}', $this->hubMock->inputUpdateGroup[0]->getData());
    }

    public function testHandleWithLoadEntityFailedException(): void
    {
        $this->roomByUserIdLoaderMock->outputQuizRoomResults = [];

        $userId = new Id('test');
        $this->deleteHandler->handle($userId);

        self::assertSame($userId->value, $this->roomByUserIdLoaderMock->inputUserId);
        self::assertSame($userId->value, $this->userByIdDeleterMock->inputUserId->value);
    }
}
