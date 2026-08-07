<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Application\Tests\Unit\Mocks\QuestionByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\RoomUpdaterMock;
use CuriosityConference\Application\UseCase\Quiz\ContinueOrFinishHandler;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use CuriosityConference\Domain\Quiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContinueOrFinishHandler::class)]
#[UsesClass(CurrentQuestionNotFoundException::class)]
#[UsesClass(Id::class)]
final class ContinueOrFinishHandlerTest extends TestCase
{
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private QuestionByIdLoaderMock $questionByIdLoaderMock;
    private RoomUpdaterMock $roomUpdaterMock;
    private HubMock $hubMock;
    private LoggerMock $loggerMock;
    private ContinueOrFinishHandler $continueOrFinishHandler;

    protected function setUp(): void
    {
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->questionByIdLoaderMock = new QuestionByIdLoaderMock();
        $this->roomUpdaterMock = new RoomUpdaterMock();
        $this->loggerMock = new LoggerMock();
        $this->hubMock = new HubMock();
        $this->continueOrFinishHandler = new ContinueOrFinishHandler(
            $this->roomByIdLoaderMock,
            $this->questionByIdLoaderMock,
            $this->roomUpdaterMock,
            $this->hubMock,
            $this->loggerMock,
        );
    }

    public function testHandleWithStartedStatusIsValid(): void
    {
        $quizRoomId = 'test';
        $quiz = new Quiz(
            'test',
            'test',
        );
        $host = new User(
            'test',
            'test',
        );
        $quizRoom = new QuizRoom(
            'test',
            'test',
            $quiz,
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            $host,
            QuizRoom::STATUS_STARTED,
        );
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->hubMock->outputUpdate = 'test';
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test2',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test3',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test4',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $questionCollection;

        $this->continueOrFinishHandler->handle(new Id($quizRoomId));

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame($quiz->getId(), $this->questionByIdLoaderMock->inputQuiz->getId());
        self::assertSame($quiz->getId(), $this->roomUpdaterMock->inputQuizRoom->getQuiz()->getId());
        self::assertSame($host->getId(), $this->roomUpdaterMock->inputQuizRoom->getHost()->getId());
        self::assertSame(QuizRoom::STATUS_STARTED, $this->roomUpdaterMock->inputQuizRoom->getStatus());
        self::assertSame('test2', $this->roomUpdaterMock->inputQuizRoom->getCurrentQuestion()->getId());
        self::assertSame('quiz/room/test/continue', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"status":"started"}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testHandleWithStartedFinishedIsValid(): void
    {
        $quizRoomId = 'test';
        $quiz = new Quiz(
            'test',
            'test',
        );
        $host = new User(
            'test',
            'test',
        );
        $currentQuestion = new Question(
            'test4',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $quizRoom = new QuizRoom(
            'test',
            'test',
            $quiz,
            $currentQuestion,
            $host,
            QuizRoom::STATUS_STARTED,
        );
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->hubMock->outputUpdate = 'test';
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test2',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test3',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            $currentQuestion,
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $questionCollection;

        $this->continueOrFinishHandler->handle(new Id($quizRoomId));

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame($quiz->getId(), $this->questionByIdLoaderMock->inputQuiz->getId());
        self::assertSame($quiz->getId(), $this->roomUpdaterMock->inputQuizRoom->getQuiz()->getId());
        self::assertSame($host->getId(), $this->roomUpdaterMock->inputQuizRoom->getHost()->getId());
        self::assertSame(QuizRoom::STATUS_FINISHED, $this->roomUpdaterMock->inputQuizRoom->getStatus());
        self::assertNull($this->roomUpdaterMock->inputQuizRoom->getCurrentQuestion());
        self::assertSame('quiz/room/test/continue', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"status":"finished"}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testHandleWithNoCurrentQuestion(): void
    {
        $quizRoomId = 'test';
        $quiz = new Quiz(
            'test',
            'test',
        );
        $host = new User(
            'test',
            'test',
        );

        $quizRoom = new QuizRoom(
            'test',
            'test',
            $quiz,
            null,
            $host,
            QuizRoom::STATUS_STARTED,
        );
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $questionCollection = new QuestionCollection([
            new Question(
                'test',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test2',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
            new Question(
                'test3',
                'test',
                $quiz,
                new ArrayCollection(),
            ),

            new Question(
                'test4',
                'test',
                $quiz,
                new ArrayCollection(),
            ),
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $questionCollection;

        try {
            $this->continueOrFinishHandler->handle(new Id($quizRoomId));
            $this->fail('CurrentQuestionNotFoundException was expected to be thrown.');
        } catch (CurrentQuestionNotFoundException $exception) {
            self::assertSame('No current question for quiz.', $exception->getMessage());
        }

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Current question is not found for quiz room.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
