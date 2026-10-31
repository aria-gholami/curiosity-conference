<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\QuestionnaireAnswer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireAnswerRepository;
use CuriosityConference\Adapters\Outbound\QuestionnaireAnswer\Loader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Loader::class)]
final class LoaderTest extends TestCase
{
    private MockObject $questionnaireAnswerRepositoryMock;
    private LoggerMock $loggerMock;
    private Loader $loader;

    protected function setUp(): void
    {
        $this->questionnaireAnswerRepositoryMock = $this->createMock(QuestionnaireAnswerRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->loader = new Loader(
            $this->questionnaireAnswerRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testLoadIsValid(): void
    {
        $questionnaireAnswerGroup = [
            new QuestionnaireAnswer(
                '1',
                'test1',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
            new QuestionnaireAnswer(
                '2',
                'test2',
                new QuestionnaireQuestion(
                    '1',
                    'test1',
                ),
            ),
        ];
        $this->questionnaireAnswerRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn($questionnaireAnswerGroup);

        $questionCollection = $this->loader->load();

        self::assertSame('1', $questionCollection->data[0]->getId());
        self::assertSame('test1', $questionCollection->data[0]->getTitle());
        self::assertSame('2', $questionCollection->data[1]->getId());
        self::assertSame('test2', $questionCollection->data[1]->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questionnaire answers.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading all questionnaire answers was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithNoAnswersFound(): void
    {
        $questionnaireAnswerGroup = [];
        $this->questionnaireAnswerRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($questionnaireAnswerGroup);

        $questionCollection = $this->loader->load();

        self::assertSame(0, \count($questionCollection->data));
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questionnaire answers.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading all questionnaire answers was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithDatabaseError(): void
    {
        $this->questionnaireAnswerRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willThrowException(new \Exception('test'));

        try {
            $this->loader->load();
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading all questionnaire answers.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading all questionnaire answers was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading all questionnaire answers was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
