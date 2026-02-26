<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\CreateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\CreateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\Quiz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateController::class)]
#[UsesClass(Quiz::class)]
final class CreateControllerTest extends TestCase
{
    private CreateHandlerMock $createHandlerMock;
    private LoggerMock $loggerMock;
    private CreateController $createController;

    protected function setUp(): void
    {
        $this->createHandlerMock = new CreateHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->createController = new CreateController($this->createHandlerMock, $this->loggerMock);
    }

    public function testCreateIsValid(): void
    {
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test@web.de',
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

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"message":"Success."}', $response->getContent());
        self::assertSame($payload['title'], $this->createHandlerMock->inputQuiz->title);
        self::assertSame($payload['description'], $this->createHandlerMock->inputQuiz->description);
        self::assertSame($payload['category'], $this->createHandlerMock->inputQuiz->category);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideInvalidData')]
    public function testCreateWithInvalidPayload(array $payload, string $missingFieldName): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/quiz is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public static function provideInvalidData(): iterable
    {
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
        ];

        yield 'empty title' => [\array_replace($payload, ['title' => '']), 'quizTitle'];
        yield 'empty description' => [\array_replace($payload, ['description' => '']), 'quizDescription'];
        yield 'empty category' => [\array_replace($payload, ['category' => '']), 'quizCategory'];
    }

    public function testCreateWithPersistFailedException(): void
    {
        $this->createHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test@web.de',
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

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['title'], $this->createHandlerMock->inputQuiz->title);
        self::assertSame($payload['description'], $this->createHandlerMock->inputQuiz->description);
        self::assertSame($payload['category'], $this->createHandlerMock->inputQuiz->category);
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
