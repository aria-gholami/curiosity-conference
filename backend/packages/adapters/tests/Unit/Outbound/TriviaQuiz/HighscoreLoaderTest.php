<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\HighscoreLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreLoader::class)]
final class HighscoreLoaderTest extends TestCase
{
    private MockObject $highscoreEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private HighscoreLoader $highscoreLoader;

    protected function setUp(): void
    {
        $this->highscoreEntityRepositoryMock = $this->createMock(TriviaHighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreLoader = new HighscoreLoader(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            0,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['triviaQuizRoom' => $highscore->getTriviaQuizRoom()])
            ->willReturn($highscore);

        $highscore = $this->highscoreLoader->load($highscore->getTriviaQuizRoom());

        self::assertSame('test', $highscore->getId());
        self::assertSame(0, $highscore->getScore());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscore.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading highscore was successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            0,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['triviaQuizRoom' => $highscore->getTriviaQuizRoom()])
            ->willReturn(null);

        try {
            $this->highscoreLoader->load($highscore->getTriviaQuizRoom());
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore" was not successful.', $exception->getMessage());
        }

        self::assertSame('test', $highscore->getId());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscore.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading highscore was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading highscore was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
