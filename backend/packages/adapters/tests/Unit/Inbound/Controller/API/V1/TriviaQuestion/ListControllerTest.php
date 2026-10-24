<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\ListController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\TriviaQuestion\Mocks\LoaderMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ListController::class)]
#[UsesClass(Id::class)]
final class ListControllerTest extends TestCase
{
    private LoaderMock $loaderMock;
    private LoggerMock $loggerMock;
    private ListController $byQuizLoadController;

    protected function setUp(): void
    {
        $this->loaderMock = new LoaderMock();
        $this->loggerMock = new LoggerMock();
        $this->byQuizLoadController = new ListController(
            $this->loaderMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $questionCollection = new QuestionCollection([
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
        ]);
        $this->loaderMock->outputQuestionCollection = $questionCollection;
        $jsonResponse = $this->byQuizLoadController->__invoke();

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

    public function testLoadWithInternalServerError(): void
    {
        $questionCollection = new QuestionCollection([
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
        ]);
        $this->loaderMock->outputQuestionCollection = $questionCollection;
        $this->loaderMock->throwException = new LoadEntityFailedException('test');
        $jsonResponse = $this->byQuizLoadController->__invoke();

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
