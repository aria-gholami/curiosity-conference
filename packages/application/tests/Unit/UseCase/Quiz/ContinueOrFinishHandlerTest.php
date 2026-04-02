<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\ByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\HighscoreLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\HighscoreUpdaterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\NextQuestionPersistHandlerMock;
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
    private ByIdLoaderMock $byIdLoaderMock;
    private HighscoreLoaderMock $highscoreLoaderMock;
    private HighscoreUpdaterMock $highscoreUpdaterMock;
    private ContinueOrFinishHandler $continueOrFinishHandler;

    protected function setUp(): void
    {
        $this->nextQuestionPersistHandlerMock = new NextQuestionPersistHandlerMock();
        $this->byIdLoaderMock = new ByIdLoaderMock();
        $this->highscoreLoaderMock = new HighscoreLoaderMock();
        $this->highscoreUpdaterMock = new HighscoreUpdaterMock();
        $this->continueOrFinishHandler = new ContinueOrFinishHandler(
            $this->nextQuestionPersistHandlerMock,
            $this->byIdLoaderMock,
            $this->highscoreLoaderMock,
            $this->highscoreUpdaterMock,
        );
    }

    public function testHandleAndGetWithCorrectAnswer(): void
    {
        $answer = new Answer('test', 'test', true);
        $this->byIdLoaderMock->outputAnswer = $answer;
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

        $highscoreUpdateRequestBody = new ContinueOrFinishRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->continueOrFinishHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame('test', $this->nextQuestionPersistHandlerMock->inputQuizRoomId);
        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(1, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->byIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoomId);
        self::assertSame($user->getId(), $this->highscoreLoaderMock->inputUserId);
        self::assertSame($expectedHighscore->getId(), $this->highscoreUpdaterMock->inputHighscore->getId());
        self::assertSame($expectedHighscore->getScore(), $this->highscoreUpdaterMock->inputHighscore->getScore());
    }

    public function testHandleAndGetWithNoCorrectAnswer(): void
    {
        $answer = new Answer('test', 'test', false);
        $this->byIdLoaderMock->outputAnswer = $answer;
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

        $highscoreUpdateRequestBody = new ContinueOrFinishRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->continueOrFinishHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame('test', $this->nextQuestionPersistHandlerMock->inputQuizRoomId);
        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(0, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->byIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoomId);
        self::assertSame($user->getId(), $this->highscoreLoaderMock->inputUserId);
    }
}
