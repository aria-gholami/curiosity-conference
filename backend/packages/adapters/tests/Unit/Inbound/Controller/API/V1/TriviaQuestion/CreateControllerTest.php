<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\CreateController;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion\Mocks\CreateHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCreateDetails;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateController::class)]
#[UsesClass(QuestionCreateDetails::class)]
final class CreateControllerTest extends TestCase
{
    private CreateHandlerMock $createHandlerMock;
    private LoggerMock $loggerMock;
    private CreateController $createController;

    protected function setUp(): void
    {
        $this->createHandlerMock = new CreateHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->createController = new CreateController($this->createHandlerMock, $this->loggerMock);
    }

    public function testCreateIsValid(): void
    {
        $this->createHandlerMock->outputQuestionId = '1';
        $payload = [
            'question' => 'test',
            'quizId' => 'test',
            'answer1' => 'test',
            'answer2' => 'test',
            'answer3' => 'test',
            'answer4' => 'test',
            'correctAnswerIndex' => 0,
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

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame('{"id":"1"}', $response->getContent());
        self::assertSame($payload['question'], $this->createHandlerMock->inputQuestionCreateDetails->question);
        self::assertSame($payload['quizId'], $this->createHandlerMock->inputQuestionCreateDetails->quizId);
        self::assertSame($payload['answer1'], $this->createHandlerMock->inputQuestionCreateDetails->answer1);
        self::assertSame($payload['answer2'], $this->createHandlerMock->inputQuestionCreateDetails->answer2);
        self::assertSame($payload['answer3'], $this->createHandlerMock->inputQuestionCreateDetails->answer3);
        self::assertSame($payload['answer4'], $this->createHandlerMock->inputQuestionCreateDetails->answer4);
        self::assertSame($payload['correctAnswerIndex'], $this->createHandlerMock->inputQuestionCreateDetails->correctAnswerIndex);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideInvalidData')]
    public function testCreateWithInvalidPayload(array $payload, string $missingFieldName): void
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/trivia/question is invalid.',
                    'context' => [
                        'exceptionMessage' => EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(),
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public static function provideInvalidData(): iterable
    {
        $payload = [
            'question' => 'test',
            'quizId' => 'test',
            'answer1' => 'test',
            'answer2' => 'test',
            'answer3' => 'test',
            'answer4' => 'test',
            'correctAnswerIndex' => 0,
        ];

        yield 'empty question' => [\array_replace($payload, ['question' => '']), 'questionCreateDetailsQuestion'];
        yield 'empty quizId' => [\array_replace($payload, ['quizId' => '']), 'questionCreateDetailsQuizId'];
        yield 'empty answer1' => [\array_replace($payload, ['answer1' => '']), 'questionCreateDetailsAnswer1'];
        yield 'empty answer2' => [\array_replace($payload, ['answer2' => '']), 'questionCreateDetailsAnswer2'];
        yield 'empty answer3' => [\array_replace($payload, ['answer3' => '']), 'questionCreateDetailsAnswer3'];
        yield 'empty answer4' => [\array_replace($payload, ['answer4' => '']), 'questionCreateDetailsAnswer4'];
    }

    public function testCreateWithInvalidNumberException(): void
    {
        $payload = [
            'question' => 'test',
            'quizId' => 'test',
            'answer1' => 'test',
            'answer2' => 'test',
            'answer3' => 'test',
            'answer4' => 'test',
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

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $response->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for POST to /api/v1/trivia/question is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "questionCreateDetailsCorrectAnswerIndex" with error: "Value for "questionCreateDetailsCorrectAnswerIndex" should not be -1."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testCreateWithPersistFailedException(): void
    {
        $this->createHandlerMock->throwException = new PersistFailedException('test');
        $payload = [
            'question' => 'test',
            'quizId' => 'test',
            'answer1' => 'test',
            'answer2' => 'test',
            'answer3' => 'test',
            'answer4' => 'test',
            'correctAnswerIndex' => 0,
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

        $response = $this->createController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $response->getContent());
        self::assertSame($payload['question'], $this->createHandlerMock->inputQuestionCreateDetails->question);
        self::assertSame($payload['quizId'], $this->createHandlerMock->inputQuestionCreateDetails->quizId);
        self::assertSame($payload['answer1'], $this->createHandlerMock->inputQuestionCreateDetails->answer1);
        self::assertSame($payload['answer2'], $this->createHandlerMock->inputQuestionCreateDetails->answer2);
        self::assertSame($payload['answer3'], $this->createHandlerMock->inputQuestionCreateDetails->answer3);
        self::assertSame($payload['answer4'], $this->createHandlerMock->inputQuestionCreateDetails->answer4);
        self::assertSame($payload['correctAnswerIndex'], $this->createHandlerMock->inputQuestionCreateDetails->correctAnswerIndex);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Question could not be persisted.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
