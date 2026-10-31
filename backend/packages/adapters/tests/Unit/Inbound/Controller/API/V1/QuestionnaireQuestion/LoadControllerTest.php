<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion\LoadController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Controller\API\V1\QuestionnaireQuestion\Mocks\LoaderMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\AnswerCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadController::class)]
final class LoadControllerTest extends TestCase
{
    private LoaderMock $loaderMock;
    private LoggerMock $loggerMock;
    private LoadController $loadController;

    protected function setUp(): void
    {
        $this->loaderMock = new LoaderMock();
        $this->loggerMock = new LoggerMock();
        $this->loadController = new LoadController($this->loaderMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $questionnaireAnswerGroup = [
            new QuestionnaireAnswer(
                '1',
                'test1',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
            new QuestionnaireAnswer(
                '1',
                'test1',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
        ];
        $this->loaderMock->outputAnswerCollection = new AnswerCollection($questionnaireAnswerGroup);
        $jsonResponse = $this->loadController->__invoke();

        self::assertSame(Response::HTTP_OK, $jsonResponse->getStatusCode());
        $jsonResponseAsArray = \json_decode($jsonResponse->getContent(), true);
        self::assertCount(2, $jsonResponseAsArray['data']);
        foreach ($jsonResponseAsArray['data'] as $jsonResponseItem) {
            self::assertIsArray($jsonResponseItem);
            self::assertSame(['question', 'answer'], \array_keys($jsonResponseItem));
            self::assertSame('1', $jsonResponseItem['question']['id']);
            self::assertSame('test1', $jsonResponseItem['question']['title']);
            self::assertSame('1', $jsonResponseItem['answer']['id']);
            self::assertSame('test1', $jsonResponseItem['answer']['title']);
        }
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $questionnaireAnswerGroup = [
            new QuestionnaireAnswer(
                '1',
                'test1',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
            new QuestionnaireAnswer(
                '2',
                'test1',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
        ];
        $this->loaderMock->outputAnswerCollection = new AnswerCollection($questionnaireAnswerGroup);
        $this->loaderMock->throwException = new LoadEntityFailedException('test');
        $jsonResponse = $this->loadController->__invoke();

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $jsonResponse->getStatusCode());
        self::assertSame('{"message":"Internal Server Error."}', $jsonResponse->getContent());
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Questionnaire questions could not be loaded.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
