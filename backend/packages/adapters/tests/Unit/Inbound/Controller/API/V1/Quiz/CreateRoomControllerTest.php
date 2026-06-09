<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\CreateRoomController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom as QuizRoomEntity;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\CreateRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\QuizRoom;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateRoomController::class)]
#[UsesClass(QuizRoom::class)]
final class CreateRoomControllerTest extends TestCase
{
    private CreateRoomHandlerMock $createRoomHandlerMock;
    private HubMock $hubMock;
    private LoggerMock $loggerMock;
    private CreateRoomController $createRoomController;

    protected function setUp(): void
    {
        $this->createRoomHandlerMock = new CreateRoomHandlerMock();
        $this->hubMock = new HubMock();
        $this->loggerMock = new LoggerMock();
        $this->createRoomController = new CreateRoomController(
            $this->createRoomHandlerMock,
            $this->hubMock,
            $this->loggerMock,
        );
    }

    public function testCreateRoomIsValid(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $quizRoom = new QuizRoomEntity(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoomEntity::STATUS_STARTED,
        );
        $quizRoom->addParticipant($user);
        $quizRoom->addParticipant($user);
        $this->createRoomHandlerMock->outputQuizRoom = $quizRoom;
        $this->hubMock->outputUpdate = 'test';
        $payload = [
            'quizId' => 'test',
            'hostId' => 'test',
            'title' => 'test',
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createRoomController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"id":"test","title":"test","quiz":{"title":"test"},"host":{"id":"test","userName":"test"},"status":"started","participants":[{"userId":"test","userName":"test"}]}', $response->getContent());
        self::assertSame($payload['hostId'], $this->createRoomHandlerMock->inputQuizRoom->hostId);
        self::assertSame($payload['quizId'], $this->createRoomHandlerMock->inputQuizRoom->quizId);
        self::assertSame($payload['title'], $this->createRoomHandlerMock->inputQuizRoom->title);
        self::assertSame('quiz/room/list', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"id":"test","title":"test","quiz":{"title":"test"},"host":{"id":"test","userName":"test"},"status":"started","participants":[{"userId":"test","userName":"test"}],"action":"add"}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testCreateRoomWithEmptyStringException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $quizRoom = new QuizRoomEntity(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoomEntity::STATUS_STARTED,
        );
        $quizRoom->addParticipant($user);
        $quizRoom->addParticipant($user);
        $this->createRoomHandlerMock->outputQuizRoom = $quizRoom;
        $payload = [
            'quizId' => '',
            'hostId' => 'test',
            'title' => 'test',
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createRoomController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/quiz/room is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty('quizRoomQuizId')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateRoomWithPersistFailedException(): void
    {
        $this->createRoomHandlerMock->throwException = new PersistFailedException('test');
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $quizRoom = new QuizRoomEntity(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoomEntity::STATUS_STARTED,
        );
        $quizRoom->addParticipant($user);
        $quizRoom->addParticipant($user);
        $this->createRoomHandlerMock->outputQuizRoom = $quizRoom;
        $payload = [
            'quizId' => 'test',
            'hostId' => 'test',
            'title' => 'test',
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['hostId'], $this->createRoomHandlerMock->inputQuizRoom->hostId);
        self::assertSame($payload['quizId'], $this->createRoomHandlerMock->inputQuizRoom->quizId);
        self::assertSame($payload['title'], $this->createRoomHandlerMock->inputQuizRoom->title);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'New quiz room could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateRoomWithLoadEntityFailedException(): void
    {
        $this->createRoomHandlerMock->throwException = new LoadEntityFailedException('test');
        $quiz = new Quiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $title = 'test';
        $quizRoom = new QuizRoomEntity(
            'test',
            $title,
            $quiz,
            null,
            $user,
            QuizRoomEntity::STATUS_STARTED,
        );
        $quizRoom->addParticipant($user);
        $quizRoom->addParticipant($user);
        $this->createRoomHandlerMock->outputQuizRoom = $quizRoom;
        $payload = [
            'quizId' => 'test',
            'hostId' => 'test',
            'title' => 'test',
        ];
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['hostId'], $this->createRoomHandlerMock->inputQuizRoom->hostId);
        self::assertSame($payload['quizId'], $this->createRoomHandlerMock->inputQuizRoom->quizId);
        self::assertSame($payload['title'], $this->createRoomHandlerMock->inputQuizRoom->title);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'New quiz room could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
