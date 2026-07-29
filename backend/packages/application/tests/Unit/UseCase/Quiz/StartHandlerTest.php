<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Tests\Unit\Mocks\HubMock;
use CuriosityConference\Application\Tests\Unit\Mocks\QuestionByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\HighscorePersisterMock;
use CuriosityConference\Application\Tests\Unit\UseCase\Quiz\Mocks\RoomUpdaterMock;
use CuriosityConference\Application\UseCase\Quiz\StartHandler;
use CuriosityConference\Domain\Question\QuestionCollection;
use CuriosityConference\Domain\Quiz\Id;
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
    private QuestionByIdLoaderMock $questionByIdLoaderMock;
    private HighscorePersisterMock $highscorePersisterMock;
    private HubMock $hubMock;
    private StartHandler $startHandler;

    protected function setUp(): void
    {
        $this->roomUpdaterMock = new RoomUpdaterMock();
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->questionByIdLoaderMock = new QuestionByIdLoaderMock();
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

        $quiz = new Quiz(
            'test',
            'test',
        );
        $host = new User(
            'test',
            'test',
        );
        $initialQuestion = new Question(
            'initial-question',
            'test',
            $quiz,
            new ArrayCollection(),
        );

        $quizRoom = new QuizRoom(
            'test',
            'test',
            $quiz,
            $initialQuestion,
            $host,
            QuizRoom::STATUS_CREATED,
        );
        $quizRoom->addParticipant($host);
        $quizRoom->addParticipant($host);
        $quizRoom->addParticipant($host);
        $quizRoom->addParticipant($host);
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
        ]);
        $this->questionByIdLoaderMock->outputQuestionCollection = $questionCollection;

        $this->startHandler->handle($quizRoomId);

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertSame('test', $this->roomUpdaterMock->inputQuizRoom->getId());
        self::assertSame('test', $this->roomUpdaterMock->inputQuizRoom->getCurrentQuestion()->getId());
        self::assertSame(QuizRoom::STATUS_STARTED, $this->roomUpdaterMock->inputQuizRoom->getStatus());
        self::assertSame('test', $this->questionByIdLoaderMock->inputQuiz->getId());
        self::assertSame('test', $this->highscorePersisterMock->inputQuizRoom->getId());
        self::assertSame('test', $this->highscorePersisterMock->inputQuizRoom->getCurrentQuestion()->getId());
        self::assertSame(QuizRoom::STATUS_STARTED, $this->highscorePersisterMock->inputQuizRoom->getStatus());
        self::assertSame('test', $this->highscorePersisterMock->inputUser->getId());
        self::assertSame('quiz/room/test/status', $this->hubMock->inputUpdateGroup[0]->getTopics()[0]);
        self::assertSame('{"action":"start","quizRoomId":"test"}', $this->hubMock->inputUpdateGroup[0]->getData());
    }
}
