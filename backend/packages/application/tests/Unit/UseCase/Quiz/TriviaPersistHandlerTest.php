<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question as QuestionEntity;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz as QuizEntity;
use CuriosityConference\Application\Tests\Unit\UseCase\Question\Mocks\PersisterMock as QuestionPersisterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\ByIdDeleterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\ByNameLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\ByQuizDeleterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\LoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\PersisterMock;
use CuriosityConference\Application\UseCase\Quiz\TriviaPersistHandler;
use CuriosityConference\Domain\Answer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Question\Question;
use CuriosityConference\Domain\Quiz\Id;
use CuriosityConference\Domain\Quiz\Quiz;
use CuriosityConference\Domain\Quiz\QuizCollection;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TriviaPersistHandler::class)]
#[UsesClass(Id::class)]
#[UsesClass(Quiz::class)]
final class TriviaPersistHandlerTest extends TestCase
{
    private LoaderMock $quizLoaderMock;
    private ByIdDeleterMock $quizByIdDeleterMock;
    private ByNameLoaderMock $quizByNameLoaderMock;
    private PersisterMock $quizPersisterMock;
    private ByQuizDeleterMock $quizByQuizDeleterMock;
    private QuestionPersisterMock $questionPersisterMock;
    private TriviaPersistHandler $triviaPersistHandler;

    protected function setUp(): void
    {
        $this->quizLoaderMock = new LoaderMock();
        $this->quizByIdDeleterMock = new ByIdDeleterMock();
        $this->quizByNameLoaderMock = new ByNameLoaderMock();
        $this->quizPersisterMock = new PersisterMock();
        $this->quizByQuizDeleterMock = new ByQuizDeleterMock();
        $this->questionPersisterMock = new QuestionPersisterMock();
        $this->triviaPersistHandler = new TriviaPersistHandler(
            $this->quizLoaderMock,
            $this->quizByIdDeleterMock,
            $this->quizByNameLoaderMock,
            $this->quizPersisterMock,
            $this->quizByQuizDeleterMock,
            $this->questionPersisterMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $quizzes = [
            [
                'title' => 'test1',
                'questions' => [
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
                ],
            ],
        ];
        $quiz1 = new QuizEntity(
            'test1',
            'test',
        );
        $quizCollection = new QuizCollection([
            $quiz1,
            new QuizEntity(
                'test2',
                'test',
            ),
            new QuizEntity(
                'test3',
                'test',
            ),
            new QuizEntity(
                'test4',
                'test',
            ),
        ]);
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            $quiz1,
            new ArrayCollection(),
        );
        $this->quizLoaderMock->outputQuizCollection = $quizCollection;
        $this->quizByNameLoaderMock->outputQuiz = [$quiz1];
        $this->quizPersisterMock->outputId = $quiz1->getId();
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($quizzes);

        self::assertEquals([
            new Id('test1'),
            new Id('test2'),
            new Id('test3'),
            new Id('test4'),
        ], $this->quizByIdDeleterMock->inputId);
        self::assertSame('test1', $this->quizByNameLoaderMock->inputTitle);
        self::assertSame($quiz1->getId(), $this->quizByQuizDeleterMock->inputQuiz->getId());
        self::assertEquals(new Question(
            $quiz1,
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

    public function testHandleWithEmptyQuizCollection(): void
    {
        $quizzes = [
            [
                'title' => 'test1',
                'questions' => [
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
                ],
            ],
        ];
        $quiz1 = new QuizEntity(
            'test1',
            'test',
        );
        $quizCollection = new QuizCollection([]);
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            $quiz1,
            new ArrayCollection(),
        );
        $this->quizLoaderMock->outputQuizCollection = $quizCollection;
        $this->quizLoaderMock->throwException = new LoadEntityFailedException('test');
        $this->quizByNameLoaderMock->outputQuiz = [$quiz1];
        $this->quizPersisterMock->outputId = $quiz1->getId();
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($quizzes);

        self::assertSame('test1', $this->quizByNameLoaderMock->inputTitle);
        self::assertSame($quiz1->getId(), $this->quizByQuizDeleterMock->inputQuiz->getId());
        self::assertEquals(new Question(
            $quiz1,
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

    public function testHandleWithQuizNotFound(): void
    {
        $quizzes = [
            [
                'title' => 'test1',
                'questions' => [
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
                                'correct' => null,
                            ],
                            [
                                'answer' => '6',
                                'correct' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $quiz1 = new QuizEntity(
            'test1',
            'test',
        );
        $quizCollection = new QuizCollection([
            $quiz1,
            new QuizEntity(
                'test2',
                'test',
            ),
            new QuizEntity(
                'test3',
                'test',
            ),
            new QuizEntity(
                'test4',
                'test',
            ),
        ]);
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            $quiz1,
            new ArrayCollection(),
        );
        $this->quizLoaderMock->outputQuizCollection = $quizCollection;
        $this->quizByNameLoaderMock->outputQuiz = [$quiz1, null];
        $this->quizPersisterMock->outputId = $quiz1->getId();
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        $this->triviaPersistHandler->handle($quizzes);

        self::assertEquals([
            new Id('test1'),
            new Id('test2'),
            new Id('test3'),
            new Id('test4'),
        ], $this->quizByIdDeleterMock->inputId);
        self::assertSame('test1', $this->quizPersisterMock->inputQuiz->title);
        self::assertSame('test1', $this->quizByNameLoaderMock->inputTitle);
        self::assertSame($quiz1->getId(), $this->quizByQuizDeleterMock->inputQuiz->getId());
        self::assertEquals(new Question(
            $quiz1,
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

    public function testHandleWithCorrectAnswerNotExistingException(): void
    {
        $quizzes = [
            [
                'title' => 'test1',
                'questions' => [
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
                ],
            ],
        ];
        $quiz1 = new QuizEntity(
            'test1',
            'test1',
        );
        $quizCollection = new QuizCollection([
            $quiz1,
            new QuizEntity(
                'test2',
                'test',
            ),
            new QuizEntity(
                'test3',
                'test',
            ),
            new QuizEntity(
                'test4',
                'test',
            ),
        ]);
        $questionEntity = new QuestionEntity(
            'test',
            'test',
            $quiz1,
            new ArrayCollection(),
        );
        $this->quizLoaderMock->outputQuizCollection = $quizCollection;
        $this->quizByNameLoaderMock->outputQuiz = [$quiz1];
        $this->quizPersisterMock->outputId = $quiz1->getId();
        $this->questionPersisterMock->outputQuestionEntity = $questionEntity;

        try {
            $this->triviaPersistHandler->handle($quizzes);
            $this->fail('CorrectAnswerNotExistingException was expected to be thrown.');
        } catch (CorrectAnswerNotExistingException $exception) {
            self::assertSame('Question must have a correct answer.', $exception->getMessage());
        }

        self::assertEquals([
            new Id('test2'),
            new Id('test3'),
            new Id('test4'),
        ], $this->quizByIdDeleterMock->inputId);
        self::assertSame('test1', $this->quizByNameLoaderMock->inputTitle);
        self::assertSame($quiz1->getId(), $this->quizByQuizDeleterMock->inputQuiz->getId());
    }
}
