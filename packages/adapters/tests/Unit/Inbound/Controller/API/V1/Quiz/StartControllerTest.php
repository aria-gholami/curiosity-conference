<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\StartController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\StartHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(StartController::class)]
#[UsesClass(Id::class)]
final class StartControllerTest extends TestCase
{
    private StartHandlerMock $startHandlerMock;
    private LoggerMock $loggerMock;
    private StartController $startController;

    protected function setUp(): void
    {
        $this->startHandlerMock = new StartHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->startController = new StartController($this->startHandlerMock, $this->loggerMock);
    }

    public function testStartUpdatedIsValid(): void
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
        $response = $this->startController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['quizRoomId'], $this->startHandlerMock->inputQuizRoomId->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testStartUpdatedWithEmptyStringException(): void
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
        $response = $this->startController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for PUT to /api/v1/quiz/room/start is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testStartUpdatedWithUpdateFailedException(): void
    {
        $this->startHandlerMock->throwException = new UpdateFailedException('test');
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
        $response = $this->startController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room could not be started.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
