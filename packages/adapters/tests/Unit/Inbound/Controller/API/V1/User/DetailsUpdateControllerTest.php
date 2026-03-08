<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\DetailsUpdateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\DetailsUpdateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DetailsUpdateController::class)]
#[UsesClass(UserDetails::class)]
final class DetailsUpdateControllerTest extends TestCase
{
    private DetailsUpdateHandlerMock $detailsUpdateHandlerMock;
    private LoggerMock $loggerMock;
    private DetailsUpdateController $detailsUpdateController;

    protected function setUp(): void
    {
        $this->detailsUpdateHandlerMock = new DetailsUpdateHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->detailsUpdateController = new DetailsUpdateController(
            $this->detailsUpdateHandlerMock,
            $this->loggerMock,
        );
    }

    public function testUpdateDetailsIsValid(): void
    {
        $payload = [
            'id' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
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

        $response = $this->detailsUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        self::assertSame($payload['id'], $this->detailsUpdateHandlerMock->inputUserDetails->id);
        self::assertSame($payload['firstName'], $this->detailsUpdateHandlerMock->inputUserDetails->firstName);
        self::assertSame($payload['lastName'], $this->detailsUpdateHandlerMock->inputUserDetails->lastName);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testUpdateDetailsWithEmptyStringException(): void
    {
        $payload = [
            'id' => '',
            'firstName' => 'test',
            'lastName' => 'test',
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

        $response = $this->detailsUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for PUT to /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty('userDetailsId')->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateDetailsWithUpdateFailedException(): void
    {
        $this->detailsUpdateHandlerMock->throwException = new UpdateFailedException('test');
        $payload = [
            'id' => 'test',
            'firstName' => 'test',
            'lastName' => 'test',
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

        $response = $this->detailsUpdateController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User details could not be updated.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
