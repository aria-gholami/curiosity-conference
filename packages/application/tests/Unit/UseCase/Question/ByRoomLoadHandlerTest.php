<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\UseCase\Question\ByRoomLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByRoomLoadHandler::class)]
#[UsesClass(Id::class)]
final class ByRoomLoadHandlerTest extends TestCase
{
    private RoomByIdLoaderMock $roomByIdLoaderMock;
    private LoggerMock $loggerMock;
    private ByRoomLoadHandler $byRoomLoadHandler;

    protected function setUp(): void
    {
        $this->roomByIdLoaderMock = new RoomByIdLoaderMock();
        $this->loggerMock = new LoggerMock();
        $this->byRoomLoadHandler = new ByRoomLoadHandler($this->roomByIdLoaderMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
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
        $expectedQuestion = new Question(
            'test',
            'test',
            $quiz,
            new ArrayCollection(),
        );
        $quizRoom = new QuizRoom('test', $quiz, $expectedQuestion, $user, QuizRoom::STATUS_STARTED);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $quizRoomId = new Id('test');
        $question = $this->byRoomLoadHandler->handleAndGet($quizRoomId);

        self::assertSame('test', $question->getId());
        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
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
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $quizRoomId = new Id('test');
        try {
            $this->byRoomLoadHandler->handleAndGet($quizRoomId);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Question" was not successful.', $exception->getMessage());
        }

        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Quiz room has no current question.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
