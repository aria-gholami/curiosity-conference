<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\DeleteRoomController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\DeleteRoomHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteRoomController::class)]
#[UsesClass(Id::class)]
final class DeleteRoomControllerTest extends TestCase
{
    private DeleteRoomHandlerMock $deleteRoomHandlerMock;
    private LoggerMock $loggerMock;
    private DeleteRoomController $deleteRoomController;

    protected function setUp(): void
    {
        $this->deleteRoomHandlerMock = new DeleteRoomHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->deleteRoomController = new DeleteRoomController(
            $this->deleteRoomHandlerMock,
            $this->loggerMock,
        );
    }

    public function testDeleteRoomIsValid(): void
    {
        $request = new Request(
            ['quizRoomId' => 'test'],
        );

        $jsonResponse = $this->deleteRoomController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->deleteRoomHandlerMock->inputQuizRoomId->value);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testDeleteRoomWithEmptyStringException(): void
    {
        $request = new Request(
            ['quizRoomId' => ''],
        );

        $jsonResponse = $this->deleteRoomController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for DELETE to /api/v1/quiz/room is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteRoomWithDeleteFailedException(): void
    {
        $this->deleteRoomHandlerMock->throwException = new DeleteFailedException('test');
        $request = new Request(
            ['quizRoomId' => 'test'],
        );

        $jsonResponse = $this->deleteRoomController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
