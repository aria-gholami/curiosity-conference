<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Device\DeleteController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device\Mocks\DeleteHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteController::class)]
#[UsesClass(RegistrationToken::class)]
final class DeleteControllerTest extends TestCase
{
    private DeleteHandlerMock $deleteHandlerMock;
    private LoggerMock $loggerMock;
    private DeleteController $deleteController;

    protected function setUp(): void
    {
        $this->deleteHandlerMock = new DeleteHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->deleteController = new DeleteController(
            $this->deleteHandlerMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $payload = [
            'registrationToken' => 'test registration token 1',
        ];
        $request = new Request([
            'registrationToken' => 'test registration token 1',
        ]);

        $response = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['registrationToken'], $this->deleteHandlerMock->inputRegistrationToken->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testDeleteWithBadRequest(): void
    {
        $request = new Request([
            'registrationToken' => '',
        ]);

        $response = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for DELETE to /api/v1/device is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "registrationToken" with error: "Value for "registrationToken" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithPersistFailedException(): void
    {
        $this->deleteHandlerMock->throwException = new DeleteFailedException('test');

        $request = new Request([
            'registrationToken' => 'test registration token 3',
        ]);

        $response = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Device could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
