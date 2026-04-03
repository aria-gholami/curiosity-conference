<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Adapters\Outbound\Quiz\HighscoreUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreUpdater::class)]
final class HighscoreUpdaterTest extends TestCase
{
    private MockObject $highscoreEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private HighscoreUpdater $highscoreUpdater;

    protected function setUp(): void
    {
        $this->highscoreEntityRepositoryMock = $this->createMock(HighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreUpdater = new HighscoreUpdater(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testUpdateIsValid(): void
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
            1,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('flush');

        $this->highscoreUpdater->update($highscore);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating highscore.',
                    'context' => [
                        'highscoreId' => 'test',
                    ],
                ],
                [
                    'message' => 'Updating highscore was successful.',
                    'context' => [
                        'highscoreId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpdateWithUpdateFailedException(): void
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
            1,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('flush')
            ->willThrowException(new UpdateFailedException('test'));

        try {
            $this->highscoreUpdater->update($highscore);
            $this->fail('UpdateFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating highscore.',
                    'context' => [
                        'highscoreId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating highscore was not successful.',
                    'context' => [
                        'highscoreId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating highscore was not successful.',
                    'context' => [
                        'highscoreId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
