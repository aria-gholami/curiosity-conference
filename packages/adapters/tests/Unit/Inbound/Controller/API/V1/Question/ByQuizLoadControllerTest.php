<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Question\ByQuizLoadController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Question\Mocks\ByQuizLoadHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ByQuizLoadController::class)]
#[UsesClass(Id::class)]
final class ByQuizLoadControllerTest extends TestCase
{
    private ByQuizLoadHandlerMock $byQuizLoadHandlerMock;
    private LoggerMock $loggerMock;
    private ByQuizLoadController $byQuizLoadController;

    protected function setUp(): void
    {
        $this->byQuizLoadHandlerMock = new ByQuizLoadHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->byQuizLoadController = new ByQuizLoadController(
            $this->byQuizLoadHandlerMock,
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
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->byQuizLoadHandlerMock->outputQuestionCollection = $questionCollection;
        $request = new Request(
            ['quizId' => 'test'],
        );
        $jsonResponse = $this->byQuizLoadController->__invoke($request);

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertCount(3, $jsonResponseAsArray['data']);
        foreach ($jsonResponseAsArray['data'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['id', 'title'], \array_keys($jsonResponseItem));
            self::assertSame('test', $jsonResponseItem['id']);
            self::assertSame('test', $jsonResponseItem['title']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithBadRequest(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->byQuizLoadHandlerMock->outputQuestionCollection = $questionCollection;
        $request = new Request();
        $jsonResponse = $this->byQuizLoadController->__invoke($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Bad Request."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Request data for GET to /api/v1/question is invalid.',
                    'context' => [
                        'exceptionMessage' => 'Validation failed for value "id" with error: "Value for "id" should not be empty."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithInternalServerError(): void
    {
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->byQuizLoadHandlerMock->outputQuestionCollection = $questionCollection;
        $this->byQuizLoadHandlerMock->throwException = new LoadEntityFailedException('test');
        $request = new Request(
            ['quizId' => 'test'],
        );
        $jsonResponse = $this->byQuizLoadController->__invoke($request);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Questions could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
