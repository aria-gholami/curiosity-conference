<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\HighscoreUpdater;
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
        $this->highscoreEntityRepositoryMock = $this->createMock(TriviaHighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreUpdater = new HighscoreUpdater(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testUpdateIsValid(): void
    {
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
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
        $quiz = new TriviaQuiz(
            'test',
            'test',
        );
        $user = new User(
            'test',
            'test',
        );
        $quizRoom = new TriviaQuizRoom('test', 'test', $quiz, null, $user, TriviaQuizRoom::STATUS_STARTED);
        $highscore = new TriviaHighscore(
            'test',
            $quizRoom,
            1,
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('flush')
            ->willThrowException(new UpdateFailedException('test'));

        try {
            $this->highscoreUpdater->update($highscore);
            $this->fail('UpdateFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore" was not successful.', $exception->getMessage());
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
