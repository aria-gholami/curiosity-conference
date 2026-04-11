<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ListController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\Quiz\Mocks\ListHandlerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ListController::class)]
#[UsesClass(Quiz::class)]
final class ListControllerTest extends TestCase
{
    private ListHandlerMock $listHandlerMock;
    private LoggerMock $loggerMock;
    private ListController $listController;

    protected function setUp(): void
    {
        $this->listHandlerMock = new ListHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->listController = new ListController($this->listHandlerMock, $this->loggerMock);
    }

    public function testListIsValid(): void
    {
        $quizCollection = new QuizCollection([
            new Quiz(
                'test',
                'test',
            ),
            new Quiz(
                'test',
                'test',
            ),
            new Quiz(
                'test',
                'test',
            ),
        ]);
        $this->listHandlerMock->outputQuizCollection = $quizCollection;

        $jsonResponse = $this->listController->__invoke();

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

    public function testListWithLoadEntityFailedException(): void
    {
        $quizCollection = new QuizCollection([
            new Quiz(
                'test',
                'test',
            ),
            new Quiz(
                'test',
                'test',
            ),
            new Quiz(
                'test',
                'test',
            ),
        ]);
        $this->listHandlerMock->outputQuizCollection = $quizCollection;
        $this->listHandlerMock->throwException = new LoadEntityFailedException('test');

        $jsonResponse = $this->listController->__invoke();

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quizes could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
