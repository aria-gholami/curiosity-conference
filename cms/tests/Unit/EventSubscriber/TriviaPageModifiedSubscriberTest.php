<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\TriviaPageModifiedSubscriber;
use App\Tests\Unit\EventSubscriber\Mocks\PageModifiedBackendSendHandlerMock;
use App\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Sulu\Page\Domain\Model\Page;

#[CoversClass(TriviaPageModifiedSubscriber::class)]
final class TriviaPageModifiedSubscriberTest extends TestCase
{
    private PageModifiedBackendSendHandlerMock $pageModifiedBackendSendHandlerMock;
    private LoggerMock $loggerMock;
    private TriviaPageModifiedSubscriber $triviaPageModifiedSubscriber;

    protected function setUp(): void
    {
        $this->pageModifiedBackendSendHandlerMock = new PageModifiedBackendSendHandlerMock();
        $this->loggerMock = new LoggerMock();
        $this->triviaPageModifiedSubscriber = new TriviaPageModifiedSubscriber(
            $this->pageModifiedBackendSendHandlerMock,
            $this->loggerMock,
        );
    }

    public function testOnPageModifiedIsValid(): void
    {
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
        ], $this->pageModifiedBackendSendHandlerMock->inputQuizzes);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithNotTriviaTitle(): void
    {
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

        self::assertEquals([], $this->pageModifiedBackendSendHandlerMock->inputQuizzes);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testOnPageModifiedWithCouldNotSendTriviaModifiedDataException(): void
    {
        $this->pageModifiedBackendSendHandlerMock->throwCouldNotSendTriviaModifiedDataException = new CouldNotSendTriviaModifiedDataException('test');
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
        ], $this->pageModifiedBackendSendHandlerMock->inputQuizzes);
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
