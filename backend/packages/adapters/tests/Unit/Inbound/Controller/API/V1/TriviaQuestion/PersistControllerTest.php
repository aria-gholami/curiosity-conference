<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\PersistController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuiz\Mocks\PersistHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PersistController::class)]
final class PersistControllerTest extends TestCase
{
    private PersistHandlerMock $triviaPersistHandlerMock;
    private LoggerMock $loggerMock;
    private PersistController $persistController;

    protected function setUp(): void
    {
        $this->triviaPersistHandlerMock = new PersistHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->persistController = new PersistController(
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
        $response = $this->persistController->__invoke($request);

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
        $response = $this->persistController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/trivia/questions is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Request data for /api/v1/trivia/questions is invalid.',
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
        $response = $this->persistController->__invoke($request);

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
        $response = $this->persistController->__invoke($request);

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
        $response = $this->persistController->__invoke($request);

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
