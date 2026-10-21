<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\JoinRoomController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\JoinRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\TriviaQuiz\JoinQuizRoom;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(JoinRoomController::class)]
#[UsesClass(JoinQuizRoom::class)]
final class JoinRoomControllerTest extends TestCase
{
    private JoinRoomHandlerMock $joinRoomHandlerMock;
    private HubMock $hubMock;
    private LoggerMock $loggerMock;
    private JoinRoomController $joinRoomController;

    protected function setUp(): void
    {
        $this->joinRoomHandlerMock = new JoinRoomHandlerMock();
        $this->hubMock = new HubMock();
        $this->loggerMock = new LoggerMock();
        $this->joinRoomController = new JoinRoomController(
            $this->joinRoomHandlerMock,
            $this->hubMock,
            $this->loggerMock,
        );
    }

    public function testJoinRoomIsValid(): void
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
        $expectedQuizRoom->addTriviaParticipant($user);
        $expectedQuizRoom->addTriviaParticipant($user);
        $this->joinRoomHandlerMock->outputQuizRoom = $expectedQuizRoom;
        $this->hubMock->outputUpdate = 'test';
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
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

        $response = $this->joinRoomController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"id":"test","title":"test","quiz":{"title":"test"},"host":{"id":"test","userName":"test"},"status":"created","participants":[{"userId":"test","userName":"test"}]}', $response->getContent());
        self::assertSame($payload['quizRoomId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->quizRoomId);
        self::assertSame($payload['userId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->userId);
        self::assertSame(\sprintf('trivia/quiz/room/%s/participants', $expectedQuizRoom->getId()), $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"action":"join","participants":[{"userId":"test","userName":"test"}]}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertSame('trivia/quiz/room/list', $this->hubMock->inputUpdateGroup[1]->getTopics()[0]);
        self::assertSame('{"action":"updateParticipants","quizRoomId":"test","participants":[{"userId":"test","userName":"test"}]}', $this->hubMock->inputUpdateGroup[1]->getData());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testJoinRoomWithEmptyStringException(): void
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
        $this->joinRoomHandlerMock->outputQuizRoom = $expectedQuizRoom;
        $payload = [
            'quizRoomId' => '',
            'userId' => 'test',
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

        $response = $this->joinRoomController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/trivia/quiz/room/join is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty('joinQuizRoomId')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testJoinRoomWithLoadEntityFailedException(): void
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
        $expectedQuizRoom->addTriviaParticipant($user);
        $expectedQuizRoom->addTriviaParticipant($user);
        $this->joinRoomHandlerMock->outputQuizRoom = $expectedQuizRoom;
        $this->joinRoomHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
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

        $response = $this->joinRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['quizRoomId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->quizRoomId);
        self::assertSame($payload['userId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->userId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room could not be joined.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testJoinRoomWithUpdateFailedException(): void
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
        $expectedQuizRoom->addTriviaParticipant($user);
        $expectedQuizRoom->addTriviaParticipant($user);
        $this->joinRoomHandlerMock->outputQuizRoom = $expectedQuizRoom;
        $this->joinRoomHandlerMock->throwException = new UpdateFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
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

        $response = $this->joinRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['quizRoomId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->quizRoomId);
        self::assertSame($payload['userId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->userId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room could not be joined.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testJoinRoomWithQuizRoomIsAlreadyStartedException(): void
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
        $expectedQuizRoom->addTriviaParticipant($user);
        $expectedQuizRoom->addTriviaParticipant($user);
        $this->joinRoomHandlerMock->outputQuizRoom = $expectedQuizRoom;
        $this->joinRoomHandlerMock->throwException = new QuizRoomIsAlreadyStartedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
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

        $response = $this->joinRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['quizRoomId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->quizRoomId);
        self::assertSame($payload['userId'], $this->joinRoomHandlerMock->inputJoinQuizRoom->userId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room could not be joined.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
