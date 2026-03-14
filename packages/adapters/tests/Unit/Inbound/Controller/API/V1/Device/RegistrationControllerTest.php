<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Device\RegistrationController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Device\Mocks\RegistrationHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Device\DeviceRegistration;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RegistrationController::class)]
#[UsesClass(DeviceRegistration::class)]
final class RegistrationControllerTest extends TestCase
{
    private RegistrationHandlerMock $registrationHandlerMock;
    private LoggerMock $loggerMock;
    private RegistrationController $registrationController;

    protected function setUp(): void
    {
        $this->registrationHandlerMock = new RegistrationHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->registrationController = new RegistrationController(
            $this->registrationHandlerMock,
            $this->loggerMock,
        );
    }

    public function testCreateIsValid(): void
    {
        $payload = [
            'registrationToken' => 'test registration token 1',
            'userId' => 'test id 1',
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

        $response = $this->registrationController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"message":"Device is registered."}', $response->getContent());
        self::assertSame($payload['registrationToken'], $this->registrationHandlerMock->inputDeviceRegistration->registrationToken);
        self::assertSame($payload['userId'], $this->registrationHandlerMock->inputDeviceRegistration->userId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testCreateWithBadRequest(): void
    {
        $payload = [
            'registrationToken' => '',
            'userId' => 'test id 2',
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

        $response = $this->registrationController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for /api/v1/device is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "deviceRegistrationToken" with error: "Value for "deviceRegistrationToken" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithPersistFailedException(): void
    {
        $this->registrationHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
            'registrationToken' => 'test registration token 3',
            'userId' => 'test id 3',
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

        $response = $this->registrationController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Device could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithLoadEntityFailedException(): void
    {
        $this->registrationHandlerMock->throwException = new LoadEntityFailedException('test');
        $payload = [
            'registrationToken' => 'test registration token 3',
            'userId' => 'test id 3',
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

        $response = $this->registrationController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Device could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
