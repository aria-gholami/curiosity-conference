<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\PasswordUpdateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\PasswordUpdateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PasswordUpdateController::class)]
#[UsesClass(UserPassword::class)]
final class PasswordUpdateControllerTest extends TestCase
{
    private PasswordUpdateHandlerMock $passwordUpdateHandlerMock;
    private LoggerMock $loggerMock;
    private PasswordUpdateController $passwordUpdateController;

    protected function setUp(): void
    {
        $this->passwordUpdateHandlerMock = new PasswordUpdateHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordUpdateController = new PasswordUpdateController(
            $this->passwordUpdateHandlerMock,
            $this->loggerMock,
        );
    }

    public function testUpdateDetailsIsValid(): void
    {
        $payload = [
            'email' => 'test',
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

        $response = $this->passwordUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['email'], $this->passwordUpdateHandlerMock->inputUserPassword->email);
        self::assertSame($payload['password'], $this->passwordUpdateHandlerMock->inputUserPassword->password);
        self::assertSame($payload['confirmPassword'], $this->passwordUpdateHandlerMock->inputUserPassword->confirmPassword);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testUpdateDetailsWithEmptyStringException(): void
    {
        $payload = [
            'email' => '',
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

        $response = $this->passwordUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for PUT to /api/v1/user/password is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty('userPasswordEmail')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateDetailsWithInvalidPasswordException(): void
    {
        $payload = [
            'email' => 'test',
            'password' => 'Test123!',
            'confirmPassword' => 'Test124!',
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

        $response = $this->passwordUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for PUT to /api/v1/user/password is invalid.',
                    'context' => [
                        'exceptionMessage' => InvalidPasswordException::passwordNotSameAsConfirmPassword('userPasswordConfirmPassword')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateDetailsWithUpdateFailedException(): void
    {
        $this->passwordUpdateHandlerMock->throwException = new UpdateFailedException('test');
        $payload = [
            'email' => 'test',
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

        $response = $this->passwordUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User password could not be updated.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
