<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion\PersistController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion\Mocks\PersistHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PersistController::class)]
final class PersistControllerTest extends TestCase
{
    private PersistHandlerMock $persistHandlerMock;
    private LoggerMock $loggerMock;
    private PersistController $persistController;

    protected function setUp(): void
    {
        $this->persistHandlerMock = new PersistHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->persistController = new PersistController($this->persistHandlerMock, $this->loggerMock);
    }

    public function testPersistIsValid(): void
    {
        $payload = [
            'test' => 'test',
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
        self::assertEquals($payload, $this->persistHandlerMock->inputQuestions);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testPersistWithInvalidQuestionDataByCMSException(): void
    {
        $payload = null;
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

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/questionnaire/questions is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "questionDataByCMS" with error: "Value for "questionDataByCMS" should be an array."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithEmptyAnswerArrayException(): void
    {
        $this->persistHandlerMock->throwException = new EmptyAnswerArrayException('test');
        $payload = [
            'test' => 'test',
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

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/questionnaire/questions is invalid.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithEmptyStringException(): void
    {
        $this->persistHandlerMock->throwException = new EmptyStringException('test');
        $payload = [
            'test' => 'test',
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

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/questionnaire/questions is invalid.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithDeleteFailedException(): void
    {
        $this->persistHandlerMock->throwException = new DeleteFailedException('test');
        $payload = [
            'test' => 'test',
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
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Questionnaire Question data could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithPersistFailedException(): void
    {
        $this->persistHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
            'test' => 'test',
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
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Questionnaire Question data could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
