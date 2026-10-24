<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Adapters\Outbound\TriviaQuestion\Loader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Loader::class)]
final class LoaderTest extends TestCase
{
    private MockObject $questionEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private Loader $byIdLoader;

    protected function setUp(): void
    {
        $this->questionEntityRepositoryMock = $this->createMock(TriviaQuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new Loader(
            $this->questionEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $questionGroup = [
            new TriviaQuestion(
                '1',
                'test1',
                new ArrayCollection(),
            ),
            new TriviaQuestion(
                '2',
                'test2',
                new ArrayCollection(),
            ),
        ];
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn($questionGroup);

        $questionCollection = $this->byIdLoader->load();

        self::assertSame('1', $questionCollection->data[0]->getId());
        self::assertSame('test1', $questionCollection->data[0]->getTitle());
        self::assertSame('2', $questionCollection->data[1]->getId());
        self::assertSame('test2', $questionCollection->data[1]->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questions.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading all questions was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithNoQuestionsFound(): void
    {
        $questionGroup = [];
        $this->questionEntityRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($questionGroup);

        $questionCollection = $this->byIdLoader->load();

        self::assertSame(0, \count($questionCollection->data));
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questions.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading all questions was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithDatabaseError(): void
    {
        $this->questionEntityRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willThrowException(new \Exception('test'));

        try {
            $this->byIdLoader->load();
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questions.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading all questions was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading all questions was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
