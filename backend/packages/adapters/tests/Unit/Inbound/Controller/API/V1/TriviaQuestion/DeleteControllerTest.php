<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\DeleteController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion\Mocks\DeleteHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
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
        $request = new Request(
            ['id' => 'test'],
        );

        $jsonResponse = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->deleteHandlerMock->inputId->value);
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
                    'message' => 'Request data for DELETE to /api/v1/trivia/question is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $this->deleteHandlerMock->throwException = new DeleteFailedException('test');
        $request = new Request(
            ['id' => 'test'],
        );

        $jsonResponse = $this->deleteController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
