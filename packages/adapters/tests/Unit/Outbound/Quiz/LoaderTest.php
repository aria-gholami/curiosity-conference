<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz as QuizEntity;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Adapters\Outbound\Quiz\Loader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Quiz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Loader::class)]
#[UsesClass(Quiz::class)]
final class LoaderTest extends TestCase
{
    private MockObject $quizEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private Loader $loader;

    protected function setUp(): void
    {
        $this->quizEntityRepositoryMock = $this->createMock(QuizRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->loader = new Loader($this->quizEntityRepositoryMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $quizGroup = [
            new QuizEntity(
                'test',
                'test',
                'test',
                'test',
            ),
            new QuizEntity(
                'test',
                'test',
                'test',
                'test',
            ),
            new QuizEntity(
                'test',
                'test',
                'test',
                'test',
            ),
        ];
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($quizGroup);

        $quizCollection = $this->loader->load();

        self::assertSame('test', $quizCollection->data[0]->getId());
        self::assertSame('test', $quizCollection->data[0]->getTitle());
        self::assertSame('test', $quizCollection->data[0]->getDescription());
        self::assertSame('test', $quizCollection->data[0]->getCategory());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quizes.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading quizes was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithEmptyQuizGroups(): void
    {
        $quizGroup = [];
        $this->quizEntityRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($quizGroup);

        try {
            $this->loader->load();
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Domain\Quiz\Quiz" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading quizes.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading quizes was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading quizes was not successful.',
                    'context' => [
                        'exceptionMessage' => 'Load entity "CuriosityConference\Domain\Quiz\Quiz" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
