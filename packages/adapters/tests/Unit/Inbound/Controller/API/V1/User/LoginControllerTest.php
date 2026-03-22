<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\LoginController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\LoginHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use CuriosityConference\Domain\User\Model\UserLoginResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoginController::class)]
#[UsesClass(UserLogin::class)]
#[UsesClass(UserLoginResponse::class)]
final class LoginControllerTest extends TestCase
{
    private LoginHandlerMock $loginHandlerMock;
    private LoggerMock $loggerMock;
    private LoginController $loginController;

    protected function setUp(): void
    {
        $this->loginHandlerMock = new LoginHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->loginController = new LoginController($this->loginHandlerMock, $this->loggerMock);
    }

    public function testLoginIsValid(): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id', 'test token', false);
        $this->loginHandlerMock->outputLoginHandler = $expectedUserLoginResponse;
        $payload = [
            'email' => 'Max.mustermann@example.com',
            'password' => 'Str0ng!PassWord',
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

        $jsonResponse = $this->loginController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        self::assertSame('{"id":"test id","token":"test token","isAdmin":false}', $jsonResponse->getContent());
        $expectedUserLogin = new UserLogin('max.mustermann@example.com', 'Str0ng!PassWord');
        self::assertSame($expectedUserLogin->email, $this->loginHandlerMock->inputUserLogin->email);
        self::assertSame($expectedUserLogin->password, $this->loginHandlerMock->inputUserLogin->password);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoginWithEmailEmptyStringException(): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id 2', 'test token 2', false);
        $this->loginHandlerMock->outputLoginHandler = $expectedUserLoginResponse;
        $payload = [
            'email' => null,
            'password' => 'Str0ng!PassWord',
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

        $jsonResponse = $this->loginController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/user/login is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userLoginEmail" with error: "Value for "userLoginEmail" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoginWithPasswordEmptyStringException(): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id 3', 'test token 3', false);
        $this->loginHandlerMock->outputLoginHandler = $expectedUserLoginResponse;
        $payload = [
            'email' => 'Max.mustermann@example.com',
            'password' => null,
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

        $jsonResponse = $this->loginController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/user/login is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userLoginPassword" with error: "Value for "userLoginPassword" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoginWithUserLoginFailedException(): void
    {
        $expectedUserLoginResponse = new UserLoginResponse('test id 4', 'test token 4', false);
        $this->loginHandlerMock->outputLoginHandler = $expectedUserLoginResponse;
        $this->loginHandlerMock->throwException = UserLoginFailedException::loginFailed('max.mustermann@example.com');
        $payload = [
            'email' => 'Max.mustermann@example.com',
            'password' => 'Str0ng!PassWord',
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

        $jsonResponse = $this->loginController->__invoke($request);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Unauthorized."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User login failed.',
                    'context' => [
                        'exceptionMessage' => 'Login user "max.mustermann@example.com" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
