<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\UserByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\ByIdLoaderMock as AnswerByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\HighscoreLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\HighscoreUpdaterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\NextQuestionPersistHandlerMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\UseCase\Quiz\ContinueOrFinishHandler;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContinueOrFinishHandler::class)]
#[UsesClass(ContinueOrFinishRequestBody::class)]
final class ContinueOrFinishHandlerTest extends TestCase
{
    private NextQuestionPersistHandlerMock $nextQuestionPersistHandlerMock;
    private AnswerByIdLoaderMock $answerByIdLoaderMock;
    private UserByIdLoaderMock $userByIdLoaderMock;
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private HighscoreLoaderMock $highscoreLoaderMock;
    private HighscoreUpdaterMock $highscoreUpdaterMock;
    private ContinueOrFinishHandler $continueOrFinishHandler;

    protected function setUp(): void
    {
        $this->nextQuestionPersistHandlerMock = new NextQuestionPersistHandlerMock();
        $this->answerByIdLoaderMock = new AnswerByIdLoaderMock();
        $this->userByIdLoaderMock = new UserByIdLoaderMock();
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->highscoreLoaderMock = new HighscoreLoaderMock();
        $this->highscoreUpdaterMock = new HighscoreUpdaterMock();
        $this->continueOrFinishHandler = new ContinueOrFinishHandler(
            $this->nextQuestionPersistHandlerMock,
            $this->answerByIdLoaderMock,
            $this->userByIdLoaderMock,
            $this->roomByIdLoaderMock,
            $this->highscoreLoaderMock,
            $this->highscoreUpdaterMock,
        );
    }

    public function testHandleAndGetWithCorrectAnswer(): void
    {
        $answer = new Answer('test', 'test', true);
        $this->answerByIdLoaderMock->outputAnswer = $answer;
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $quizRoom = new QuizRoom('test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $expectedHighscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            0,
        );
        $this->highscoreLoaderMock->outputHighscore = $expectedHighscore;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $highscoreUpdateRequestBody = new ContinueOrFinishRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->continueOrFinishHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame('test', $this->nextQuestionPersistHandlerMock->inputQuizRoomId);
        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(1, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->answerByIdLoaderMock->inputId);
        self::assertSame($user->getId(), $this->userByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->roomByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoom->getId());
        self::assertSame($user->getId(), $this->highscoreLoaderMock->inputUser->getId());
        self::assertSame($expectedHighscore->getId(), $this->highscoreUpdaterMock->inputHighscore->getId());
        self::assertSame($expectedHighscore->getScore(), $this->highscoreUpdaterMock->inputHighscore->getScore());
    }

    public function testHandleAndGetWithNoCorrectAnswer(): void
    {
        $answer = new Answer('test', 'test', false);
        $this->answerByIdLoaderMock->outputAnswer = $answer;
        $quiz = new Quiz(
            'test',
            'test',
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $quizRoom = new QuizRoom('test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $expectedHighscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            0,
        );
        $this->highscoreLoaderMock->outputHighscore = $expectedHighscore;
        $this->userByIdLoaderMock->outputUser = $user;
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $highscoreUpdateRequestBody = new ContinueOrFinishRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->continueOrFinishHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame('test', $this->nextQuestionPersistHandlerMock->inputQuizRoomId);
        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(0, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->answerByIdLoaderMock->inputId);
        self::assertSame($user->getId(), $this->userByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->roomByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoom->getId());
        self::assertSame($user->getId(), $this->highscoreLoaderMock->inputUser->getId());
    }
}
