<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\TriviaPersistController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\TriviaPersistHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Answer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(TriviaPersistController::class)]
final class TriviaPersistControllerTest extends TestCase
{
    private TriviaPersistHandlerMock $triviaPersistHandlerMock;
    private LoggerMock $loggerMock;
    private TriviaPersistController $triviaPersistController;

    protected function setUp(): void
    {
        $this->triviaPersistHandlerMock = new TriviaPersistHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->triviaPersistController = new TriviaPersistController(
            $this->triviaPersistHandlerMock,
            $this->loggerMock,
        );
    }

    public function testPersistTriviaIsValid(): void
    {
        $payload = [
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
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"message":"Success."}', $response->getContent());
        self::assertSame($payload, $this->triviaPersistHandlerMock->inputQuizzes);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testPersistTriviaWithEmptyStringException(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/trivia/quizzes is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Request data for /api/v1/trivia/quizzes is invalid.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistTriviaWithPersistFailedException(): void
    {
        $this->triviaPersistHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
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
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload, $this->triviaPersistHandlerMock->inputQuizzes);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistTriviaWithDeleteFailedException(): void
    {
        $this->triviaPersistHandlerMock->throwException = new DeleteFailedException('test');
        $payload = [
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
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload, $this->triviaPersistHandlerMock->inputQuizzes);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistTriviaWithLoadEntityFailedException(): void
    {
        $this->triviaPersistHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
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
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload, $this->triviaPersistHandlerMock->inputQuizzes);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistTriviaCorrectAnswerNotExistingException(): void
    {
        $this->triviaPersistHandlerMock->throwException = new CorrectAnswerNotExistingException('test');
        $payload = [
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
        $response = $this->triviaPersistController->__invoke($request);

        self::assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
        self::assertSame('{"message":"Conflict."}', $response->getContent());
        self::assertSame($payload, $this->triviaPersistHandlerMock->inputQuizzes);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
