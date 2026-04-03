<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Answer;

use CuriosityConference\Adapters\Outbound\Answer\ByIdLoader;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\AnswerRepository;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdLoader::class)]
final class ByIdLoaderTest extends TestCase
{
    private MockObject $answerEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdLoader $byIdLoader;

    protected function setUp(): void
    {
        $this->answerEntityRepositoryMock = $this->createMock(AnswerRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new ByIdLoader(
            $this->answerEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $answer = new Answer(
            'test',
            'test',
            false,
        );
        $this->answerEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $answer->getId()])
            ->willReturn($answer);

        $answer = $this->byIdLoader->load($answer->getId());

        self::assertSame('test', $answer->getId());
        self::assertSame('test', $answer->getTitle());
        self::assertFalse($answer->isCorrect());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading answer by answer id.',
                    'context' => [
                        'answerId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading answer by answer id was successful.',
                    'context' => [
                        'answerId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithNoAnswerFound(): void
    {
        $answer = new Answer(
            'test',
            'test',
            false,
        );
        $this->answerEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $answer->getId()])
            ->willReturn(null);

        try {
            $this->byIdLoader->load($answer->getId());
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading answer by answer id.',
                    'context' => [
                        'answerId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading answer by answer id was not successful.',
                    'context' => [
                        'answerId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading answer by answer id was not successful.',
                    'context' => [
                        'answerId' => 'test',
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
