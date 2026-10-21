<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\ByIdLoaderMock as AnswerByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\HighscoreLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\HighscoreUpdaterMock;
use CuriosityConference\Application\UseCase\TriviaQuiz\HighscoreUpdateHandler;
use CuriosityConference\Domain\TriviaQuiz\HighscoreUpdateRequestBody;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreUpdateHandler::class)]
#[UsesClass(HighscoreUpdateRequestBody::class)]
final class HighscoreUpdateHandlerTest extends TestCase
{
    private AnswerByIdLoaderMock $answerByIdLoaderMock;
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private HighscoreLoaderMock $highscoreLoaderMock;
    private HighscoreUpdaterMock $highscoreUpdaterMock;
    private HighscoreUpdateHandler $highscoreUpdateHandler;

    protected function setUp(): void
    {
        $this->answerByIdLoaderMock = new AnswerByIdLoaderMock();
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->highscoreLoaderMock = new HighscoreLoaderMock();
        $this->highscoreUpdaterMock = new HighscoreUpdaterMock();
        $this->highscoreUpdateHandler = new HighscoreUpdateHandler(
            $this->answerByIdLoaderMock,
            $this->roomByIdLoaderMock,
            $this->highscoreLoaderMock,
            $this->highscoreUpdaterMock,
        );
    }

    public function testHandleAndGetWithCorrectAnswer(): void
    {
        $answer = new TriviaAnswer('test', 'test', true);
        $this->answerByIdLoaderMock->outputAnswer = $answer;
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);
        $expectedHighscore = new TriviaHighscore(
            'test',
            $quizRoom,
            0,
        );
        $this->highscoreLoaderMock->outputHighscore = $expectedHighscore;
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $highscoreUpdateRequestBody = new HighscoreUpdateRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->highscoreUpdateHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(1, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->answerByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->roomByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoom->getId());
        self::assertSame($expectedHighscore->getId(), $this->highscoreUpdaterMock->inputHighscore->getId());
        self::assertSame($expectedHighscore->getScore(), $this->highscoreUpdaterMock->inputHighscore->getScore());
    }

    public function testHandleAndGetWithNoCorrectAnswer(): void
    {
        $answer = new TriviaAnswer('test', 'test', false);
        $this->answerByIdLoaderMock->outputAnswer = $answer;
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);
        $expectedHighscore = new TriviaHighscore(
            'test',
            $quizRoom,
            0,
        );
        $this->highscoreLoaderMock->outputHighscore = $expectedHighscore;
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $highscoreUpdateRequestBody = new HighscoreUpdateRequestBody('test', 'test', 'test');
        $highscoreUpdateResponse = $this->highscoreUpdateHandler->handleAndGet($highscoreUpdateRequestBody);

        self::assertSame($expectedHighscore->getId(), $highscoreUpdateResponse->highscore->getId());
        self::assertSame(0, $highscoreUpdateResponse->highscore->getScore());
        self::assertSame($answer->getId(), $this->answerByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->roomByIdLoaderMock->inputId);
        self::assertSame($quizRoom->getId(), $this->highscoreLoaderMock->inputQuizRoom->getId());
    }
}
