<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\RoomByIdLoaderMock;
use CuriosityConference\Application\UseCase\TriviaQuestion\ByRoomLoadHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
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
        $user = new User(
            'test',
            'test',
        );
        $expectedQuestion = new TriviaQuestion(
            'test',
            'test',
            new ArrayCollection(),
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $expectedQuestion, $user, TriviaQuizRoom::STATUS_STARTED);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $quizRoomId = new Id('test');
        $question = $this->byRoomLoadHandler->handleAndGet($quizRoomId);

        self::assertSame('test', $question->getId());
        self::assertSame('test', $this->roomByIdLoaderMock->inputId);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $this->roomByIdLoaderMock->outputQuizRoom = $quizRoom;

        $quizRoomId = new Id('test');
        try {
            $this->byRoomLoadHandler->handleAndGet($quizRoomId);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
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
