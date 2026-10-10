<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\QuestionByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion\Mocks\QuizByIdLoaderMock;
use CuriosityConference\Application\UseCase\TriviaQuestion\ByQuizLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByQuizLoadHandler::class)]
#[UsesClass(Id::class)]
final class ByQuizLoadHandlerTest extends TestCase
{
    private QuizByIdLoaderMock $quizByIdLoaderMock;
    private QuestionByIdLoaderMock $questionByIdLoaderMock;
    private LoggerMock $loggerMock;
    private ByQuizLoadHandler $byQuizLoadHandler;

    protected function setUp(): void
    {
        $this->quizByIdLoaderMock = new QuizByIdLoaderMock();
        $this->questionByIdLoaderMock = new QuestionByIdLoaderMock();
        $this->loggerMock = new LoggerMock();
        $this->byQuizLoadHandler = new ByQuizLoadHandler(
            $this->quizByIdLoaderMock,
            $this->questionByIdLoaderMock,
            $this->loggerMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $this->quizByIdLoaderMock->outputQuiz = $quiz;
        $inputQuestionCollection = new QuestionCollection([
            new TriviaQuestion(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $inputQuestionCollection;
        $id = new Id('test');

        $questionCollection = $this->byQuizLoadHandler->handle($id);

        self::assertSame(1, \count($questionCollection->data));
        self::assertSame($id->value, $this->quizByIdLoaderMock->inputId->value);
        self::assertSame($quiz->getId(), $this->questionByIdLoaderMock->inputQuiz->getId());
        self::assertSame($quiz->getTitle(), $this->questionByIdLoaderMock->inputQuiz->getTitle());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testHandleWithLoadEntityFailedException(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $this->quizByIdLoaderMock->outputQuiz = $quiz;
        $this->quizByIdLoaderMock->throwException = new LoadEntityFailedException('test');
        $inputQuestionCollection = new QuestionCollection([
            new TriviaQuestion(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $inputQuestionCollection;
        $id = new Id('test');

        $questionCollection = $this->byQuizLoadHandler->handle($id);

        self::assertSame(0, \count($questionCollection->data));
        self::assertEquals([
            'warning' => [
                [
                    'message' => 'Quiz by id is not found.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
