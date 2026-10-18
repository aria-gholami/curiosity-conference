<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\QuestionnairePageModifiedSubscriber;
use App\Tests\Unit\EventSubscriber\Mocks\QuestionnaireExecutedCodeByCLICheckerMock;
use App\Tests\Unit\EventSubscriber\Mocks\QuestionnairePageModifiedBackendSendHandlerMock;
use App\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Sulu\Page\Domain\Model\Page;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[CoversClass(QuestionnairePageModifiedSubscriber::class)]
final class QuestionnairePageModifiedSubscriberTest extends TestCase
{
    private QuestionnaireExecutedCodeByCLICheckerMock $questionnaireExecutedCodeByCLICheckerMock;
    private QuestionnairePageModifiedBackendSendHandlerMock $questionnairePageModifiedBackendSendHandlerMock;
    private LoggerMock $loggerMock;
    private QuestionnairePageModifiedSubscriber $questionnairePageModifiedSubscriber;

    protected function setUp(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock = new QuestionnaireExecutedCodeByCLICheckerMock();
        $this->questionnairePageModifiedBackendSendHandlerMock = new QuestionnairePageModifiedBackendSendHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->questionnairePageModifiedSubscriber = new QuestionnairePageModifiedSubscriber(
            $this->questionnaireExecutedCodeByCLICheckerMock,
            $this->questionnairePageModifiedBackendSendHandlerMock,
            $this->loggerMock,
        );
    }

    public function testOnPageModifiedIsValid(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'questions' => [
                'test' => 'test',
            ],
            'showResults' => 'no',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([
            'test' => 'test',
        ], $this->questionnairePageModifiedBackendSendHandlerMock->inputQuestions);
        self::assertFalse($this->questionnairePageModifiedBackendSendHandlerMock->inputShowResults);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithShowResultsEqualsNull(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'questions' => [
                'test' => 'test',
            ],
            'showResults' => null,
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([
            'test' => 'test',
        ], $this->questionnairePageModifiedBackendSendHandlerMock->inputQuestions);
        self::assertFalse($this->questionnairePageModifiedBackendSendHandlerMock->inputShowResults);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithShowResultsEqualsYes(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'questions' => [
                'test' => 'test',
            ],
            'showResults' => 'yes',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([
            'test' => 'test',
        ], $this->questionnairePageModifiedBackendSendHandlerMock->inputQuestions);
        self::assertTrue($this->questionnairePageModifiedBackendSendHandlerMock->inputShowResults);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithExecutedCodeByCLIEqualsTrue(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = true;
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'questions' => [
                'test' => 'test',
            ],
            'showResults' => 'no',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithNotTriviaTitle(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'NotQuestionnaire',
            'questions' => 'test',
            'showResults' => 'no',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithQuizzesEqualsNull(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'showResults' => 'no',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->questionnairePageModifiedBackendSendHandlerMock->inputQuestions);
        self::assertFalse($this->questionnairePageModifiedBackendSendHandlerMock->inputShowResults);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithCouldNotSendTriviaModifiedDataException(): void
    {
        $this->questionnaireExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $this->questionnairePageModifiedBackendSendHandlerMock->throwCouldNotSendQuestionnaireModifiedDataException = new CouldNotSendQuestionnaireModifiedDataException('test');
        $page = new Page();
        $payload = [
            'title' => 'Questionnaire',
            'questions' => [
                'test' => 'test',
            ],
            'showResults' => 'no',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        try {
            $this->questionnairePageModifiedSubscriber->onPageModified($pageModifiedEvent);
            $this->fail('BadRequestHttpException was expected to be thrown.');
        } catch (BadRequestHttpException $exception) {
            self::assertSame('Questionnaire data could not be persisted: test', $exception->getMessage());
        }

        self::assertEquals([
            'test' => 'test',
        ], $this->questionnairePageModifiedBackendSendHandlerMock->inputQuestions);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Questionnaire data could not been sent.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
