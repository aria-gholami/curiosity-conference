<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\DeleteController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\DeleteHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteController::class)]
#[UsesClass(Id::class)]
final class DeleteControllerTest extends TestCase
{
    private DeleteHandlerMock $deleteHandlerMock;
    private HubMock $hubMock;
    private LoggerMock $loggerMock;
    private DeleteController $deleteController;

    protected function setUp(): void
    {
        $this->deleteHandlerMock = new DeleteHandlerMock();
        $this->hubMock = new HubMock();
        $this->loggerMock = new LoggerMock();
        $this->deleteController = new DeleteController(
            $this->deleteHandlerMock,
            $this->hubMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $request = new Request(
            ['userId' => 'test'],
        );
        $this->hubMock->outputUpdate = 'test';

        $jsonResponse = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->deleteHandlerMock->inputUserId->value);
        self::assertSame('user/delete', $this->hubMock->inputUpdate->getTopics()[0]);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testDeleteWithEmptyStringException(): void
    {
        $request = new Request(
            [],
        );

        $jsonResponse = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for DELETE to /api/v1/user is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "userId" with error: "Value for "userId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $this->deleteHandlerMock->throwException = new DeleteFailedException('test');
        $request = new Request(
            ['userId' => 'test'],
        );

        $jsonResponse = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
