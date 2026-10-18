<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion;

use CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks\PersisterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks\QuestionResetterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\QuestionnaireQuestion\Mocks\UpserterMock;
use CuriosityConference\Application\UseCase\QuestionnaireQuestion\PersistHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PersistHandler::class)]
#[UsesClass(Question::class)]
final class PersistHandlerTest extends TestCase
{
    private QuestionResetterMock $questionResetterMock;
    private PersisterMock $persisterMock;
    private UpserterMock $upserterMock;
    private PersistHandler $persistHandler;

    protected function setUp(): void
    {
        $this->questionResetterMock = new QuestionResetterMock();
        $this->persisterMock = new PersisterMock();
        $this->upserterMock = new UpserterMock();
        $this->persistHandler = new PersistHandler($this->questionResetterMock, $this->persisterMock, $this->upserterMock);
    }

    public function testHandleIsValid(): void
    {
        $questions = [
            [
                'question' => 'test',
                'answers' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ],
        ];
        $this->persistHandler->handle($questions, false);

        self::assertTrue($this->questionResetterMock->resetWasCalled);
        self::assertSame('test', $this->persisterMock->inputQuestion->title);
        self::assertEquals([
            '1',
            '2',
            '3',
            '4',
        ], $this->persisterMock->inputQuestion->answers);
        self::assertFalse($this->upserterMock->inputShowResults);
    }

    public function testHandleWithQuestionEqualsNull(): void
    {
        $questions = [
            [
                'question' => null,
                'answers' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ],
        ];
        try {
            $this->persistHandler->handle($questions, false);
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "questionnaireQuestionTitle" with error: "Value for "questionnaireQuestionTitle" should not be empty."', $exception->getMessage());
        }

        self::assertTrue($this->questionResetterMock->resetWasCalled);
    }

    public function testHandleWithAnswersEqualsNull(): void
    {
        $questions = [
            [
                'question' => 'test',
                'answers' => null,
            ],
        ];
        try {
            $this->persistHandler->handle($questions, false);
            $this->fail('EmptyAnswerArrayException was expected to be thrown.');
        } catch (EmptyAnswerArrayException $exception) {
            self::assertSame('Validation failed for value "questionnaireQuestionAnswers" with error: "Value for "questionnaireQuestionAnswers" should not be empty."', $exception->getMessage());
        }

        self::assertTrue($this->questionResetterMock->resetWasCalled);
    }
}
