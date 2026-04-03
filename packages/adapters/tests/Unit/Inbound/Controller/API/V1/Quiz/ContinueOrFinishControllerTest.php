<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ContinueOrFinishController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\ContinueOrFinishHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ContinueOrFinishController::class)]
#[UsesClass(HighscoreUpdateRequestBody::class)]
#[UsesClass(Id::class)]
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

        $response = $this->continueOrFinishController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['quizRoomId'], $this->continueOrFinishHandlerMock->inputQuizRoomId->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithLoadEntityFailedException(): void
    {
        $this->continueOrFinishHandlerMock->throwException = new LoadEntityFailedException('test');
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
        $payload = [
            'quizRoomId' => '',
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
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testContinueQuestionWithUpdateFailedException(): void
    {
        $this->continueOrFinishHandlerMock->throwException = new UpdateFailedException('test');
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
        $this->continueOrFinishHandlerMock->throwException = new CurrentQuestionNotFoundException('test');
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
