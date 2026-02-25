<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\CreateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\CreateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(CreateController::class)]
#[UsesClass(User::class)]
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

    public function testHandleIsValid(): void
    {
        $expectedUserId = 'test';
        $this->createHandlerMock->outputUserId = $expectedUserId;

        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'test',
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

        self::assertSame(201, $response->getStatusCode());
        self::assertSame('{"id":"test"}', $response->getContent());
        self::assertSame($payload['firstName'], $this->createHandlerMock->inputUser->firstName);
        self::assertSame($payload['lastName'], $this->createHandlerMock->inputUser->lastName);
        self::assertSame($payload['email'], $this->createHandlerMock->inputUser->email);
        self::assertSame($payload['password'], $this->createHandlerMock->inputUser->password);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideInvalidData')]
    public function testHandleWithInvalidPayload(array $payload, string $missingFieldName): void
    {
        $expectedUserId = 'test';
        $this->createHandlerMock->outputUserId = $expectedUserId;

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

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/user is invalid.',
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
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'test',
        ];

        yield 'empty firstName' => [\array_replace($payload, ['firstName' => '']), 'userFirstName'];
        yield 'empty lastName' => [\array_replace($payload, ['lastName' => '']), 'userLastName'];
        yield 'empty email' => [\array_replace($payload, ['email' => '']), 'userEmail'];
    }

    public function testHandleWithInvalidEmail(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test',
            'password' => 'test',
        ];

        $expectedUserId = 'test';
        $this->createHandlerMock->outputUserId = $expectedUserId;

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

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => InvalidEmailException::emailIsInvalid('user')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testHandleWithInternalServerError(): void
    {
        $expectedUserId = 'test';
        $this->createHandlerMock->outputUserId = $expectedUserId;
        $this->createHandlerMock->thrownException = new PersistFailedException('test');

        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'test',
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

        self::assertSame(500, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['firstName'], $this->createHandlerMock->inputUser->firstName);
        self::assertSame($payload['lastName'], $this->createHandlerMock->inputUser->lastName);
        self::assertSame($payload['email'], $this->createHandlerMock->inputUser->email);
        self::assertSame($payload['password'], $this->createHandlerMock->inputUser->password);
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
