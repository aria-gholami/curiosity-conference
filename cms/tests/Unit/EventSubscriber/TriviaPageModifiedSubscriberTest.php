<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\TriviaPageModifiedSubscriber;
use App\Tests\Unit\EventSubscriber\Mocks\TriviaExecutedCodeByCLICheckerMock;
use App\Tests\Unit\EventSubscriber\Mocks\TriviaPageModifiedBackendSendHandlerMock;
use App\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Sulu\Page\Domain\Model\Page;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[CoversClass(TriviaPageModifiedSubscriber::class)]
final class TriviaPageModifiedSubscriberTest extends TestCase
{
    private TriviaExecutedCodeByCLICheckerMock $triviaExecutedCodeByCLICheckerMock;
    private TriviaPageModifiedBackendSendHandlerMock $triviaPageModifiedBackendSendHandlerMock;
    private LoggerMock $loggerMock;
    private TriviaPageModifiedSubscriber $triviaPageModifiedSubscriber;

    protected function setUp(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock = new TriviaExecutedCodeByCLICheckerMock();
        $this->triviaPageModifiedBackendSendHandlerMock = new TriviaPageModifiedBackendSendHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->triviaPageModifiedSubscriber = new TriviaPageModifiedSubscriber(
            $this->triviaExecutedCodeByCLICheckerMock,
            $this->triviaPageModifiedBackendSendHandlerMock,
            $this->loggerMock,
        );
    }

    public function testOnPageModifiedIsValid(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Trivia',
            'quizzes' => [
                'test' => 'test',
            ],
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->triviaPageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([
            'test' => 'test',
        ], $this->triviaPageModifiedBackendSendHandlerMock->inputQuizzes);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithExecutedCodeByCLIEqualsTrue(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock->outputExecutedByCLI = true;
        $page = new Page();
        $payload = [
            'title' => 'Trivia',
            'quizzes' => [
                'test' => 'test',
            ],
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->triviaPageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithNotTriviaTitle(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'NotTrivia',
            'quizzes' => 'test',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->triviaPageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithQuizzesEqualsNull(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $page = new Page();
        $payload = [
            'title' => 'Trivia',
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        $this->triviaPageModifiedSubscriber->onPageModified($pageModifiedEvent);

        self::assertEquals([], $this->triviaPageModifiedBackendSendHandlerMock->inputQuizzes);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithCouldNotSendTriviaModifiedDataException(): void
    {
        $this->triviaExecutedCodeByCLICheckerMock->outputExecutedByCLI = false;
        $this->triviaPageModifiedBackendSendHandlerMock->throwCouldNotSendTriviaModifiedDataException = new CouldNotSendTriviaModifiedDataException('test');
        $page = new Page();
        $payload = [
            'title' => 'Trivia',
            'quizzes' => [
                'test' => 'test',
            ],
        ];
        $pageModifiedEvent = new PageModifiedEvent(
            $page,
            'en',
            $payload,
        );
        try {
            $this->triviaPageModifiedSubscriber->onPageModified($pageModifiedEvent);
            $this->fail('BadRequestHttpException was expected to be thrown.');
        } catch (BadRequestHttpException $exception) {
            self::assertSame('Trivia data could not be persisted: test', $exception->getMessage());
        }

        self::assertEquals([
            'test' => 'test',
        ], $this->triviaPageModifiedBackendSendHandlerMock->inputQuizzes);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Trivia data could not been sent.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
