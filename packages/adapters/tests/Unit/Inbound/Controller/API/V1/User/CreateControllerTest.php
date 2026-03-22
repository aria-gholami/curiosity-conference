<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\CreateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\CreateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserLoginResponse;
use CuriosityConference\Domain\User\Model\UserRegistration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateController::class)]
#[UsesClass(UserRegistration::class)]
#[UsesClass(UserLoginResponse::class)]
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
        $expectedUserLoginResponse = new UserLoginResponse('test id', 'test token', false);
        $this->createHandlerMock->outputUserLoginResponse = $expectedUserLoginResponse;

        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'Test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        self::assertSame('{"id":"test id","token":"test token"}', $response->getContent());
        self::assertSame($payload['firstName'], $this->createHandlerMock->inputUser->firstName);
        self::assertSame($payload['lastName'], $this->createHandlerMock->inputUser->lastName);
        self::assertSame('test@web.de', $this->createHandlerMock->inputUser->email);
        self::assertSame($payload['password'], $this->createHandlerMock->inputUser->password);
        self::assertSame($payload['confirmPassword'], $this->createHandlerMock->inputUser->confirmPassword);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideInvalidData')]
    public function testCreateWithInvalidPayload(array $payload, string $missingFieldName): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id 2', 'test token 2', false);
        $this->createHandlerMock->outputUserLoginResponse = $expectedUserLoginResponse;

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
            'email' => 'Test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
        ];

        yield 'empty firstName' => [\array_replace($payload, ['firstName' => '']), 'userRegistrationFirstName'];
        yield 'empty lastName' => [\array_replace($payload, ['lastName' => '']), 'userRegistrationLastName'];
        yield 'empty email' => [\array_replace($payload, ['email' => '']), 'userRegistrationEmail'];
    }

    public function testCreateWithInvalidEmail(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
        ];

        $expectedUserLoginResponse = new UserLoginResponse('test id 3', 'test token 3', false);
        $this->createHandlerMock->outputUserLoginResponse = $expectedUserLoginResponse;

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
                    'message' => 'Request data for /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => InvalidEmailException::emailIsInvalid('userRegistration')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithInvalidPassword(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'Test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test124!',
        ];

        $expectedUserLoginResponse = new UserLoginResponse('test id 4', 'test token 4', false);
        $this->createHandlerMock->outputUserLoginResponse = $expectedUserLoginResponse;

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
                    'message' => 'Request data for /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => InvalidPasswordException::passwordNotSameAsConfirmPassword('userRegistrationConfirmPassword')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithInternalServerError(): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id 5', 'test token 5', false);
        $this->createHandlerMock->outputUserLoginResponse = $expectedUserLoginResponse;
        $this->createHandlerMock->thrownException = new PersistFailedException('test');

        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'Test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        self::assertSame($payload['firstName'], $this->createHandlerMock->inputUser->firstName);
        self::assertSame($payload['lastName'], $this->createHandlerMock->inputUser->lastName);
        self::assertSame('test@web.de', $this->createHandlerMock->inputUser->email);
        self::assertSame($payload['password'], $this->createHandlerMock->inputUser->password);
        self::assertSame($payload['confirmPassword'], $this->createHandlerMock->inputUser->confirmPassword);
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
