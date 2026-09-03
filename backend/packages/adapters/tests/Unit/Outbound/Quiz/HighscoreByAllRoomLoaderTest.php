<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Adapters\Outbound\Quiz\HighscoreByAllRoomsLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizRoomHighscore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreByAllRoomsLoader::class)]
final class HighscoreByAllRoomLoaderTest extends TestCase
{
    private MockObject $highscoreEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private HighscoreByAllRoomsLoader $highscoreByAllRoomsLoader;

    protected function setUp(): void
    {
        $this->highscoreEntityRepositoryMock = $this->createMock(HighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreByAllRoomsLoader = new HighscoreByAllRoomsLoader(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $quizRoomHighscoreGroup = [
            new QuizRoomHighscore('test', 1),
            new QuizRoomHighscore('test2', 2),
            new QuizRoomHighscore('test3', 3),
        ];
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('loadFromAllRooms')
            ->willReturn($quizRoomHighscoreGroup);

        $allRoomsHighscoreCollection = $this->highscoreByAllRoomsLoader->load();

        self::assertCount(3, $allRoomsHighscoreCollection->data);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscore from all quiz rooms.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading highscore from all quiz rooms was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('loadFromAllRooms')
            ->willThrowException(new \Exception('test'));

        try {
            $this->highscoreByAllRoomsLoader->load();
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscore from all quiz rooms.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading highscore from all quiz rooms was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading highscore from all quiz rooms was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
