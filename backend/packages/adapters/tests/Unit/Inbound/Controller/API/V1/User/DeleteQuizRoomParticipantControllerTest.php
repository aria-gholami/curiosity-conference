<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\DeleteQuizRoomParticipantController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\User\Mocks\DeleteQuizRoomParticipantHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteQuizRoomParticipantController::class)]
#[UsesClass(QuizRoomParticipant::class)]
final class DeleteQuizRoomParticipantControllerTest extends TestCase
{
    private DeleteQuizRoomParticipantHandlerMock $deleteQuizRoomParticipantHandlerMock;
    private LoggerMock $loggerMock;
    private DeleteQuizRoomParticipantController $deleteQuizRoomParticipantController;

    protected function setUp(): void
    {
        $this->deleteQuizRoomParticipantHandlerMock = new DeleteQuizRoomParticipantHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->deleteQuizRoomParticipantController = new DeleteQuizRoomParticipantController(
            $this->deleteQuizRoomParticipantHandlerMock,
            $this->loggerMock,
        );
    }

    public function testDeleteParticipantIsValid(): void
    {
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');
        $request->attributes->set('participantId', 'test');

        $jsonResponse = $this->deleteQuizRoomParticipantController->__invoke($request);

        self::assertSame(Response::HTTP_NO_CONTENT, $jsonResponse->getStatusCode());
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->quizRoomId);
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->participantId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testDeleteParticipantWithEmptyStringException(): void
    {
        $request = new Request();
        $request->attributes->set('quizRoomId', '');
        $request->attributes->set('participantId', 'test');

        $jsonResponse = $this->deleteQuizRoomParticipantController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for DELETE to /api/v1/trivia/quiz/room/{id}/participant/{id} is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "quizRoomParticipantQuizRoomId" with error: "Value for "quizRoomParticipantQuizRoomId" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $this->deleteQuizRoomParticipantHandlerMock->throwException = new UpdateFailedException('test');
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');
        $request->attributes->set('participantId', 'test');

        $jsonResponse = $this->deleteQuizRoomParticipantController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->quizRoomId);
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->participantId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room participant could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithLoadEntityFailedException(): void
    {
        $this->deleteQuizRoomParticipantHandlerMock->throwException = new LoadEntityFailedException('test');
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');
        $request->attributes->set('participantId', 'test');

        $jsonResponse = $this->deleteQuizRoomParticipantController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->quizRoomId);
        self::assertSame('test', $this->deleteQuizRoomParticipantHandlerMock->inputQuizRoomParticipant->participantId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room participant could not be deleted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
