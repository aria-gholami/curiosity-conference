<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\RevealCorrectAnswerController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\RevealCorrectAnswerHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RevealCorrectAnswerController::class)]
#[UsesClass(Id::class)]
final class RevealCorrectAnswerControllerTest extends TestCase
{
    private RevealCorrectAnswerHandlerMock $revealCorrectAnswerHandlerMock;
    private LoggerMock $loggerMock;
    private RevealCorrectAnswerController $revealCorrectAnswerController;

    protected function setUp(): void
    {
        $this->revealCorrectAnswerHandlerMock = new RevealCorrectAnswerHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->revealCorrectAnswerController = new RevealCorrectAnswerController(
            $this->revealCorrectAnswerHandlerMock,
            $this->loggerMock,
        );
    }

    public function testRevealCorrectAnswerIsValid(): void
    {
        $payload = [
            'quizRoomId' => 'test',
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

        $response = $this->revealCorrectAnswerController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['quizRoomId'], $this->revealCorrectAnswerHandlerMock->inputQuizRoomId->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testRevealCorrectAnswerWithInvalidQuizRoomId(): void
    {
        $payload = [
            'quizRoomId' => null,
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

        $response = $this->revealCorrectAnswerController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/trivia/quiz/room/answer/reveal is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testRevealCorrectAnswerWithNoQuizRoomFound(): void
    {
        $this->revealCorrectAnswerHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
            'quizRoomId' => 'test',
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

        $response = $this->revealCorrectAnswerController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'TriviaAnswer could not be revealed.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
