<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Question\ByRoomLoadController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question\Mocks\ByRoomLoadHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
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
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $expectedQuestion = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection([
                new Answer(
                    'test',
                    'test',
                    false,
                ),
                new Answer(
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
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $expectedQuestion = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection([
                new Answer(
                    'test',
                    'test',
                    false,
                ),
                new Answer(
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
                    'message' => 'Request data for GET to /api/v1/quiz/room/{quizRoomId}/question is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $expectedQuestion = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection([
                new Answer(
                    'test',
                    'test',
                    false,
                ),
                new Answer(
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
