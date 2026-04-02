<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ContinueOrFinishController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\ContinueOrFinishHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use CuriosityConference\Domain\Quiz\ContinueOrFinishResponse;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ContinueOrFinishController::class)]
#[UsesClass(ContinueOrFinishRequestBody::class)]
final class ContinueOrFinishControllerTest extends TestCase
{
    private ContinueOrFinishHandlerMock $continueOrFinishHandlerMock;
    private LoggerMock $loggerMock;
    private ContinueOrFinishController $continueOrFinishController;

    protected function setUp(): void
    {
        $this->continueOrFinishHandlerMock = new ContinueOrFinishHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->continueOrFinishController = new ContinueOrFinishController(
            $this->continueOrFinishHandlerMock,
            $this->loggerMock,
        );
    }

    public function testContinueQuestionIsValid(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('{"highscore":1,"isCorrect":true}', $response->getContent());
        self::assertSame($payload['userId'], $this->continueOrFinishHandlerMock->inputHighscoreUpdateRequestBody->userId);
        self::assertSame($payload['quizRoomId'], $this->continueOrFinishHandlerMock->inputHighscoreUpdateRequestBody->quizRoomId);
        self::assertSame($payload['answerId'], $this->continueOrFinishHandlerMock->inputHighscoreUpdateRequestBody->answerId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testUpdateHighscoreWithLoadEntityFailedException(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $this->continueOrFinishHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be continues or quiz could not be finished.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithEmptyStringException(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $payload = [
            'quizRoomId' => '',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data to /api/v1/quiz/room/continue is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "continueOrFinishRequestBodyQuizRoomId" with error: "Value for "continueOrFinishRequestBodyQuizRoomId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithUpdateFailedException(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $this->continueOrFinishHandlerMock->throwException = new UpdateFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be continues or quiz could not be finished.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithPersistFailedException(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $this->continueOrFinishHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be continues or quiz could not be finished.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithCurrentQuestionNotFoundException(): void
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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            1,
        );
        $answer = new Answer(
            'test',
            'test',
            true,
        );
        $this->continueOrFinishHandlerMock->outputHighscoreUpdateResponse = new ContinueOrFinishResponse($highscore, $answer);
        $this->continueOrFinishHandlerMock->throwException = new CurrentQuestionNotFoundException('test');
        $payload = [
            'quizRoomId' => 'test',
            'userId' => 'test',
            'answerId' => 'test',
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be continues or quiz could not be finished.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
