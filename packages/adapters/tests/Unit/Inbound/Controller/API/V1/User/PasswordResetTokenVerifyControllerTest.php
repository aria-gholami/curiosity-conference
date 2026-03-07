<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\PasswordResetTokenVerifyController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\PasswordResetTokenVerifyHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PasswordResetTokenVerifyController::class)]
#[UsesClass(PasswordResetToken::class)]
final class PasswordResetTokenVerifyControllerTest extends TestCase
{
    private PasswordResetTokenVerifyHandlerMock $passwordResetTokenVerifyHandlerMock;
    private LoggerMock $loggerMock;
    private PasswordResetTokenVerifyController $passwordResetTokenVerifyController;

    protected function setUp(): void
    {
        $this->passwordResetTokenVerifyHandlerMock = new PasswordResetTokenVerifyHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordResetTokenVerifyController = new PasswordResetTokenVerifyController(
            $this->passwordResetTokenVerifyHandlerMock,
            $this->loggerMock,
        );
    }

    public function testVerifyPasswordResetTokenIsValid(): void
    {
        $this->passwordResetTokenVerifyHandlerMock->outputVerified = true;
        $request = new Request(
            ['token' => 12345678, 'id' => 'test'],
        );

        $jsonResponse = $this->passwordResetTokenVerifyController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        self::assertSame('{"verified":true}', $jsonResponse->getContent());
        self::assertSame('test', $this->passwordResetTokenVerifyHandlerMock->inputPasswordResetToken->userId);
        self::assertSame(12345678, $this->passwordResetTokenVerifyHandlerMock->inputPasswordResetToken->token);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testVerifyPasswordResetTokenWithEmptyStringException(): void
    {
        $this->passwordResetTokenVerifyHandlerMock->outputVerified = true;
        $request = new Request(
            ['token' => 12345678, 'id' => ''],
        );

        $jsonResponse = $this->passwordResetTokenVerifyController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/password/reset is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "passwordResetTokenUserId" with error: "Value for "passwordResetTokenUserId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testVerifyPasswordResetTokenWithInvalidPasswordResetTokenException(): void
    {
        $this->passwordResetTokenVerifyHandlerMock->outputVerified = true;
        $request = new Request(
            ['token' => 123456789, 'id' => 'test'],
        );

        $jsonResponse = $this->passwordResetTokenVerifyController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/password/reset is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "passwordReset" with error: "Token passwordReset must be exactly 8 digits."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testVerifyPasswordResetTokenWithPasswordResetTokenVerifyFailedException(): void
    {
        $this->passwordResetTokenVerifyHandlerMock->outputVerified = true;
        $this->passwordResetTokenVerifyHandlerMock->throwException = new PasswordResetTokenVerifyFailedException('test');
        $request = new Request(
            ['token' => 12345678, 'id' => 'test'],
        );

        $jsonResponse = $this->passwordResetTokenVerifyController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertSame('test', $this->passwordResetTokenVerifyHandlerMock->inputPasswordResetToken->userId);
        self::assertSame(12345678, $this->passwordResetTokenVerifyHandlerMock->inputPasswordResetToken->token);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Password reset token could not be verified.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
