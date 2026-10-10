<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\CreateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\CreateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Exception\InvalidNameException;
use CuriosityConference\Domain\User\Model\Id;
use CuriosityConference\Domain\User\Model\UserName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateController::class)]
#[UsesClass(UserName::class)]
#[UsesClass(Id::class)]
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
        $userId = new Id('test id');
        $this->createHandlerMock->outputUserId = $userId;

        $payload = [
            'userName' => 'test',
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
        self::assertSame('{"id":"test id","userName":"test"}', $response->getContent());
        self::assertSame($payload['userName'], $this->createHandlerMock->inputUserName->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testCreateWithEmptyId(): void
    {
        $this->createHandlerMock->thrownException = new EmptyStringException('test');
        $payload = [
            'userName' => 'test',
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

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithInvalidPayload(): void
    {
        $userId = new Id('test id');
        $this->createHandlerMock->outputUserId = $userId;
        $payload = [
            'userName' => '12345678901234567890',
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

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => InvalidNameException::isInvalid('userName')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithInternalServerError(): void
    {
        $userId = new Id('test id');
        $this->createHandlerMock->outputUserId = $userId;
        $this->createHandlerMock->thrownException = new PersistFailedException('test');

        $payload = [
            'userName' => 'test',
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
        self::assertSame($payload['userName'], $this->createHandlerMock->inputUserName->value);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
