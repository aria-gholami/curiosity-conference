<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\ByRoomLoadController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion\Mocks\ByRoomLoadHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ByRoomLoadController::class)]
#[UsesClass(Id::class)]
final class ByRoomLoadControllerTest extends TestCase
{
    private ByRoomLoadHandlerMock $byRoomLoadHandlerMock;
    private LoggerMock $loggerMock;
    private ByRoomLoadController $byRoomLoadController;

    protected function setUp(): void
    {
        $this->byRoomLoadHandlerMock = new ByRoomLoadHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->byRoomLoadController = new ByRoomLoadController(
            $this->byRoomLoadHandlerMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $expectedQuestion = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection([
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
            ]),
        );
        $this->byRoomLoadHandlerMock->outputQuestion = $expectedQuestion;
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');
        $jsonResponse = $this->byRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertSame('test', $jsonResponseAsArray['question']);
        self::assertCount(2, $jsonResponseAsArray['answers']);
        foreach ($jsonResponseAsArray['answers'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['id', 'title', 'isCorrect'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['id']);
            self::assertSame('test', $jsonResponseItem['title']);
            self::assertFalse($jsonResponseItem['isCorrect']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithEmptyStringException(): void
    {
        $expectedQuestion = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection([
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
            ]),
        );
        $this->byRoomLoadHandlerMock->outputQuestion = $expectedQuestion;
        $request = new Request();
        $request->attributes->set('quizRoomId', '');
        $jsonResponse = $this->byRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/trivia/quiz/room/{quizRoomId}/question is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $expectedQuestion = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection([
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
                new TriviaAnswer(
                    'test',
                    'test',
                    false,
                ),
            ]),
        );
        $this->byRoomLoadHandlerMock->outputQuestion = $expectedQuestion;
        $this->byRoomLoadHandlerMock->throwException = new LoadEntityFailedException('test');
        $request = new Request();
        $request->attributes->set('quizRoomId', 'test');
        $jsonResponse = $this->byRoomLoadController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertSame('test', $this->byRoomLoadHandlerMock->inputId->value);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Current question by quiz room could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
