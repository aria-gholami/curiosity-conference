<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\DetailsGetController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\DetailsGetHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DetailsGetController::class)]
#[UsesClass(Id::class)]
final class DetailsGetControllerTest extends TestCase
{
    private DetailsGetHandlerMock $detailsGetHandlerMock;
    private LoggerMock $loggerMock;
    private DetailsGetController $detailsGetController;

    protected function setUp(): void
    {
        $this->detailsGetHandlerMock = new DetailsGetHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->detailsGetController = new DetailsGetController(
            $this->detailsGetHandlerMock,
            $this->loggerMock,
        );
    }

    public function testGetUserIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->detailsGetHandlerMock->outputUser = $user;
        $request = new Request(
            ['id' => 'test'],
        );

        $jsonResponse = $this->detailsGetController->__invoke($request);

        self::assertSame('{"email":"test","firstName":"test","lastName":"test"}', $jsonResponse->getContent());
        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->detailsGetHandlerMock->inputId->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testGetUserWithBadRequest(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->detailsGetHandlerMock->outputUser = $user;
        $request = new Request(
            ['id' => ''],
        );

        $jsonResponse = $this->detailsGetController->__invoke($request);

        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userId" with error: "Value for "userId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testGetUserWithInternalServerError(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->detailsGetHandlerMock->outputUser = $user;
        $this->detailsGetHandlerMock->throwException = new LoadEntityFailedException('test');
        $request = new Request(
            ['id' => 'test'],
        );

        $jsonResponse = $this->detailsGetController->__invoke($request);

        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User details could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
