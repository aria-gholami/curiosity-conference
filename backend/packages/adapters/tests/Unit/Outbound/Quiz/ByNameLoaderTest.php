<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Adapters\Outbound\Quiz\ByNameLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByNameLoader::class)]
final class ByNameLoaderTest extends TestCase
{
    private MockObject $quizEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByNameLoader $byNameLoader;

    protected function setUp(): void
    {
        $this->quizEntityRepositoryMock = $this->createMock(QuizRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byNameLoader = new ByNameLoader(
            $this->quizEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $quizName = 'test';
        $quiz = new Quiz(
            'test',
            'test',
        );
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => $quiz->getTitle()])
            ->willReturn($quiz);

        $loadedQuiz = $this->byNameLoader->load($quizName);

        self::assertSame($quiz->getTitle(), $loadedQuiz->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz by name.',
                    'context' => [
                        'title' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading quiz by name was successful.',
                    'context' => [
                        'title' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $quizName = 'test';
        $quiz = new Quiz(
            'test',
            'test',
        );
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['title' => $quiz->getTitle()])
            ->willThrowException(new LoadEntityFailedException('test'));

        try {
            $this->byNameLoader->load($quizName);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quiz by name.',
                    'context' => [
                        'title' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quiz by name was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quiz by name was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
