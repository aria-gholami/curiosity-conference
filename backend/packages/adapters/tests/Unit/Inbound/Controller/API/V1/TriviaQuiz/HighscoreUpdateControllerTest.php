<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\HighscoreUpdateController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\HighscoreUpdateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(HighscoreUpdateController::class)]
#[UsesClass(HighscoreUpdateRequestBody::class)]
final class HighscoreUpdateControllerTest extends TestCase
{
    private HighscoreUpdateHandlerMock $highscoreUpdateHandlerMock;
    private LoggerMock $loggerMock;
    private HighscoreUpdateController $highscoreUpdateController;

    protected function setUp(): void
    {
        $this->highscoreUpdateHandlerMock = new HighscoreUpdateHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->highscoreUpdateController = new HighscoreUpdateController(
            $this->highscoreUpdateHandlerMock,
            $this->loggerMock,
        );
    }

    public function testUpdateHighscoreIsValid(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            1,
        );
        $answer = new TriviaAnswer(
            'test',
            'test',
            true,
        );
        $this->highscoreUpdateHandlerMock->outputHighscoreUpdateResponse = new HighscoreUpdateResponse($highscore, $answer);
        $payload = [
            'quizRoomId' => 'test',
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

        $response = $this->highscoreUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('{"highscore":1,"isCorrect":true}', $response->getContent());
        self::assertSame($payload['quizRoomId'], $this->highscoreUpdateHandlerMock->inputHighscoreUpdateRequestBody->quizRoomId);
        self::assertSame($payload['answerId'], $this->highscoreUpdateHandlerMock->inputHighscoreUpdateRequestBody->answerId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testUpdateHighscoreWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            1,
        );
        $answer = new TriviaAnswer(
            'test',
            'test',
            true,
        );
        $this->highscoreUpdateHandlerMock->outputHighscoreUpdateResponse = new HighscoreUpdateResponse($highscore, $answer);
        $this->highscoreUpdateHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
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

        $response = $this->highscoreUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'TriviaHighscore could not be updated.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateHighscoreWithEmptyStringException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            1,
        );
        $answer = new TriviaAnswer(
            'test',
            'test',
            true,
        );
        $this->highscoreUpdateHandlerMock->outputHighscoreUpdateResponse = new HighscoreUpdateResponse($highscore, $answer);
        $payload = [
            'quizRoomId' => '',
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

        $response = $this->highscoreUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for PUT to /api/v1/trivia/highscore is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "continueOrFinishRequestBodyQuizRoomId" with error: "Value for "continueOrFinishRequestBodyQuizRoomId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateHighscoreWithUpdateFailedException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            1,
        );
        $answer = new TriviaAnswer(
            'test',
            'test',
            true,
        );
        $this->highscoreUpdateHandlerMock->outputHighscoreUpdateResponse = new HighscoreUpdateResponse($highscore, $answer);
        $this->highscoreUpdateHandlerMock->throwException = new UpdateFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
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

        $response = $this->highscoreUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'TriviaHighscore could not be updated.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
