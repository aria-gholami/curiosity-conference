<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ActiveRoomLoadController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\ActiveRoomLoadHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ActiveRoomLoadController::class)]
#[UsesClass(Id::class)]
final class ActiveRoomLoadControllerTest extends TestCase
{
    private ActiveRoomLoadHandlerMock $activeRoomLoadHandlerMock;
    private LoggerMock $loggerMock;
    private ActiveRoomLoadController $activeRoomLoadController;

    protected function setUp(): void
    {
        $this->activeRoomLoadHandlerMock = new ActiveRoomLoadHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->activeRoomLoadController = new ActiveRoomLoadController(
            $this->activeRoomLoadHandlerMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizRoomEntity = new TriviaQuizRoom(
            'test',
            'test',
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $this->activeRoomLoadHandlerMock->outputQuizRoom = $expectedQuizRoomEntity;

        $request = new Request();
        $request->attributes->set('userId', 'test');
        $jsonResponse = $this->activeRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        self::assertSame('{"id":"test","title":"test","host":{"id":"test","userName":"test"},"status":"started","participants":[{"userId":"test","userName":"test"}]}', $jsonResponse->getContent());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithEmptyResult(): void
    {
        $expectedQuizRoomEntity = null;
        $this->activeRoomLoadHandlerMock->outputQuizRoom = $expectedQuizRoomEntity;

        $request = new Request();
        $request->attributes->set('userId', 'test');
        $jsonResponse = $this->activeRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithEmptyStringException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizRoomEntity = new TriviaQuizRoom(
            'test',
            'test',
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $this->activeRoomLoadHandlerMock->outputQuizRoom = $expectedQuizRoomEntity;

        $request = new Request();
        $request->attributes->set('userId', '');
        $jsonResponse = $this->activeRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/trivia/quiz/room/user/{userId} is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userId" with error: "Value for "userId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $expectedQuizRoomEntity = new TriviaQuizRoom(
            'test',
            'test',
            null,
            $user,
            TriviaQuizRoom::STATUS_STARTED,
        );
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $expectedQuizRoomEntity->addTriviaParticipant($user);
        $this->activeRoomLoadHandlerMock->outputQuizRoom = $expectedQuizRoomEntity;
        $this->activeRoomLoadHandlerMock->throwException = new LoadEntityFailedException('test');

        $request = new Request();
        $request->attributes->set('userId', 'test');
        $jsonResponse = $this->activeRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Active room could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
