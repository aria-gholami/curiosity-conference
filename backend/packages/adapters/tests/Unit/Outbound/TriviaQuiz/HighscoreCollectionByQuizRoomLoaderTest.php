<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuiz\HighscoreCollectionByQuizRoomLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreCollectionByQuizRoomLoader::class)]
final class HighscoreCollectionByQuizRoomLoaderTest extends TestCase
{
    private MockObject $highscoreEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private HighscoreCollectionByQuizRoomLoader $highscoreCollectionByQuizRoomLoader;

    protected function setUp(): void
    {
        $this->highscoreEntityRepositoryMock = $this->createMock(TriviaHighscoreRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->highscoreCollectionByQuizRoomLoader = new HighscoreCollectionByQuizRoomLoader(
            $this->highscoreEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
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
        $expectedHighscoreCollection = new HighscoreCollection(
            [
                new TriviaHighscore(
                    'test',
                    $quizRoom,
                    $user,
                    0,
                ),
                new TriviaHighscore(
                    'test2',
                    $quizRoom,
                    $user,
                    1,
                ),
                new TriviaHighscore(
                    'test3',
                    $quizRoom,
                    $user,
                    2,
                ),
            ],
        );
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findBy')
            ->with(['triviaQuizRoom' => $quizRoom])
            ->willReturn($expectedHighscoreCollection->data);

        $highscoreCollection = $this->highscoreCollectionByQuizRoomLoader->load($quizRoom);

        self::assertSame('test', $highscoreCollection->data[0]->getId());
        self::assertSame('test', $highscoreCollection->data[0]->getTriviaQuizRoom()->getId());
        self::assertSame('test', $highscoreCollection->data[0]->getUser()->getId());
        self::assertSame(0, $highscoreCollection->data[0]->getScore());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscores.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading highscores was successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
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
        $expectedHighscoreCollection = new HighscoreCollection([]);
        $this->highscoreEntityRepositoryMock->expects(self::once())
            ->method('findBy')
            ->with(['triviaQuizRoom' => $quizRoom])
            ->willReturn($expectedHighscoreCollection->data);

        try {
            $this->highscoreCollectionByQuizRoomLoader->load($quizRoom);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading highscores.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading highscores was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading highscores was not successful.',
                    'context' => [
                        'quizRoomId' => 'test',
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
