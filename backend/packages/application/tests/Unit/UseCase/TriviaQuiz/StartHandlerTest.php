<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Application\Tests\Unit\Mocks\QuestionLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\HighscorePersisterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuiz\Mocks\RoomUpdaterMock;
use CuriosityConference\Application\UseCase\TriviaQuiz\StartHandler;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StartHandler::class)]
#[UsesClass(Id::class)]
final class StartHandlerTest extends TestCase
{
    private RoomUpdaterMock $roomUpdaterMock;
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private QuestionLoaderMock $questionByIdLoaderMock;
    private HighscorePersisterMock $highscorePersisterMock;
    private HubMock $hubMock;
    private StartHandler $startHandler;

    protected function setUp(): void
    {
        $this->roomUpdaterMock = new RoomUpdaterMock();
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->questionByIdLoaderMock = new QuestionLoaderMock();
        $this->highscorePersisterMock = new HighscorePersisterMock();
        $this->hubMock = new HubMock();
        $this->startHandler = new StartHandler(
            $this->roomUpdaterMock,
            $this->roomByIdLoaderMock,
            $this->questionByIdLoaderMock,
            $this->highscorePersisterMock,
            $this->hubMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $quizRoomId = new Id('test');

        $host = new User(
            'test',
            'test',
        );
        $initialQuestion = new TriviaQuestion(
            'initial-question',
            'test',
            new ArrayCollection(),
        );

        $quizRoom = new TriviaQuizRoom(
            'test',
            'test',
            $initialQuestion,
            $host,
            TriviaQuizRoom::STATUS_CREATED,
        );
        $quizRoom->addTriviaParticipant($host);
        $quizRoom->addTriviaParticipant($host);
        $quizRoom->addTriviaParticipant($host);
        $quizRoom->addTriviaParticipant($host);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;
        $this->hubMock->outputUpdate = 'test';
        $questionCollection = new QuestionCollection([
            new TriviaQuestion(
                'test',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test2',
                'test',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                'test3',
                'test',
                new ArrayCollection(),
            ),
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $questionCollection;

        $this->startHandler->handle($quizRoomId);

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame('test', $this->roomUpdaterMock->inputQuizRoom->getId());
        self::assertSame('test', $this->roomUpdaterMock->inputQuizRoom->getCurrentTriviaQuestion()->getId());
        self::assertSame(TriviaQuizRoom::STATUS_STARTED, $this->roomUpdaterMock->inputQuizRoom->getStatus());
        self::assertSame('test', $this->highscorePersisterMock->inputQuizRoom->getId());
        self::assertSame('test', $this->highscorePersisterMock->inputQuizRoom->getCurrentTriviaQuestion()->getId());
        self::assertSame(TriviaQuizRoom::STATUS_STARTED, $this->highscorePersisterMock->inputQuizRoom->getStatus());
        self::assertSame('trivia/quiz/room/test/status', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"action":"started"}', $this->hubMock->inputUpdateGroup[0]->getData());
        self::assertSame('trivia/quiz/room/list', $this->hubMock->inputUpdateGroup[1]->getTopics()[0]);
        self::assertSame('{"action":"status","status":"started","quizRoomId":"test"}', $this->hubMock->inputUpdateGroup[1]->getData());
    }
}
