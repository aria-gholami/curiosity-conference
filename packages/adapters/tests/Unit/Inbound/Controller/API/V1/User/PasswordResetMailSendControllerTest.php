<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\PasswordResetMailSendController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\PasswordResetMailSendHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\User\Model\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PasswordResetMailSendController::class)]
#[UsesClass(Email::class)]
final class PasswordResetMailSendControllerTest extends TestCase
{
    private PasswordResetMailSendHandlerMock $passwordResetMailSendHandlerMock;
    private LoggerMock $loggerMock;
    private PasswordResetMailSendController $passwordResetMailSendController;

    protected function setUp(): void
    {
        $this->passwordResetMailSendHandlerMock = new PasswordResetMailSendHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordResetMailSendController = new PasswordResetMailSendController(
            $this->passwordResetMailSendHandlerMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode(['email' => 'test'], \JSON_THROW_ON_ERROR),
        );

        $jsonResponse = $this->passwordResetMailSendController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->passwordResetMailSendHandlerMock->inputEmail->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testDeleteWithEmptyStringException(): void
    {
        $request = new Request(
            [],
        );

        $jsonResponse = $this->passwordResetMailSendController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/password/reset is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userEmail" with error: "Value for "userEmail" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $this->passwordResetMailSendHandlerMock->throwException = new CouldNotSentMailException('test');
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode(['email' => 'test'], \JSON_THROW_ON_ERROR),
        );

        $jsonResponse = $this->passwordResetMailSendController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Reset password mail could not be sent.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
