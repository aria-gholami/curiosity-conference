<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion as QuestionEntity;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion\Mocks\PersisterMock as QuestionPersisterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion\Mocks\ResetterMock;
use CuriosityConference\Application\UseCase\TriviaQuestion\PersistHandler;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\TriviaQuestion\Question;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PersistHandler::class)]
#[UsesClass(Id::class)]
final class PersistHandlerTest extends TestCase
{
    private ResetterMock $resetterMock;
    private QuestionPersisterMock $questionPersisterMock;
    private PersistHandler $triviaPersistHandler;

    protected function setUp(): void
    {
        $this->resetterMock = new ResetterMock();
        $this->questionPersisterMock = new QuestionPersisterMock();
        $this->triviaPersistHandler = new PersistHandler(
            $this->resetterMock,
            $this->questionPersisterMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $questions = [
            [
                'question' => 'What is 2 + 2?',
                'answers' => [
                    [
                        'answer' => '3',
                        'correct' => false,
                    ],
                    [
                        'answer' => '4',
                        'correct' => true,
                    ],
                    [
                        'answer' => '5',
                        'correct' => false,
                    ],
                    [
                        'answer' => '6',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($questions);

        self::assertTrue($this->resetterMock->resetCalled);
        self::assertEquals(new Question(
            'What is 2 + 2?',
            [
                '3',
                '4',
                '5',
                '6',
            ],
            1,
        ), $this->questionPersisterMock->inputQuestion);
    }

    public function testHandleWith2CorrectAnswers(): void
    {
        $questions = [
            [
                'question' => 'What is 2 + 2?',
                'answers' => [
                    [
                        'answer' => '3',
                        'correct' => true,
                    ],
                    [
                        'answer' => '4',
                        'correct' => true,
                    ],
                    [
                        'answer' => '5',
                        'correct' => false,
                    ],
                    [
                        'answer' => '6',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($questions);

        self::assertTrue($this->resetterMock->resetCalled);
        self::assertEquals(new Question(
            'What is 2 + 2?',
            [
                '3',
                '4',
                '5',
                '6',
            ],
            0,
        ), $this->questionPersisterMock->inputQuestion);
    }

    public function testHandleWithNullCorrectAnswer(): void
    {
        $questions = [
            [
                'question' => 'What is 2 + 2?',
                'answers' => [
                    [
                        'answer' => '3',
                        'correct' => null,
                    ],
                    [
                        'answer' => '4',
                        'correct' => true,
                    ],
                    [
                        'answer' => '5',
                        'correct' => false,
                    ],
                    [
                        'answer' => '6',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($questions);

        self::assertTrue($this->resetterMock->resetCalled);
        self::assertEquals(new Question(
            'What is 2 + 2?',
            [
                '3',
                '4',
                '5',
                '6',
            ],
            1,
        ), $this->questionPersisterMock->inputQuestion);
    }

    public function testHandleWithNoCorrectAnswers(): void
    {
        $questions = [
            [
                'question' => 'What is 2 + 2?',
                'answers' => [
                    [
                        'answer' => '3',
                        'correct' => false,
                    ],
                    [
                        'answer' => '4',
                        'correct' => false,
                    ],
                    [
                        'answer' => '5',
                        'correct' => false,
                    ],
                    [
                        'answer' => '6',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            new ArrayCollection(),
        );
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        try {
            $this->triviaPersistHandler->handle($questions);
            $this->fail('CorrectAnswerNotExistingException was expected to be thrown.');
        } catch (CorrectAnswerNotExistingException $exception) {
            self::assertSame('Question must have a correct answer.', $exception->getMessage());
        }

        self::assertTrue($this->resetterMock->resetCalled);
    }
}
