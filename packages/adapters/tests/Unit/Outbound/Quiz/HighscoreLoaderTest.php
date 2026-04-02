<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Adapters\Outbound\Quiz\HighscoreLoader;
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
        $this->highscoreEntityRepositoryMock = $this->createMock(HighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreLoader = new HighscoreLoader(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
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
        $quizRoom = new QuizRoom('test', $quiz, null, $user, QuizRoom::STATUS_STARTED);
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            0,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['user' => $highscore->getUser(), 'quizRoom' => $highscore->getQuizRoom()])
            ->willReturn($highscore);

        $highscore = $this->highscoreLoader->load($highscore->getUser(), $highscore->getQuizRoom());

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
        $highscore = new Highscore(
            'test',
            $quizRoom,
            $user,
            0,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['user' => $highscore->getUser(), 'quizRoom' => $highscore->getQuizRoom()])
            ->willReturn(null);

        try {
            $this->highscoreLoader->load($highscore->getUser(), $highscore->getQuizRoom());
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore" was not successful.', $exception->getMessage());
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
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
