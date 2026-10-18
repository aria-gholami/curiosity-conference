<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\QuestionnaireSettings;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireSettings;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireSettingsRepository;
use CuriosityConference\Adapters\Outbound\QuestionnaireSettings\Upserter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpsertFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Upserter::class)]
final class UpserterTest extends TestCase
{
    private MockObject $questionnaireSettingsRepositoryMock;
    private LoggerMock $loggerMock;
    private Upserter $upserter;

    protected function setUp(): void
    {
        $this->questionnaireSettingsRepositoryMock = $this->createMock(QuestionnaireSettingsRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->upserter = new Upserter($this->questionnaireSettingsRepositoryMock, $this->loggerMock);
    }

    public function testUpsertIsValidWithEmptySettings(): void
    {
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn([]);
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('persist');
        $this->upserter->upsert(false);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start upserting questionnaire settings.',
                    'context' => [
                        'showResults' => false,
                    ],
                ],
                [
                    'message' => 'Upserting questionnaire settings was successful.',
                    'context' => [
                        'showResults' => false,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpsertIsValidWithExistingSettings(): void
    {
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn([
                new QuestionnaireSettings('1', false),
            ]);
        $questionnaireSettings = new QuestionnaireSettings('1', true);
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('persist')
            ->with($questionnaireSettings);
        $this->upserter->upsert(true);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start upserting questionnaire settings.',
                    'context' => [
                        'showResults' => true,
                    ],
                ],
                [
                    'message' => 'Upserting questionnaire settings was successful.',
                    'context' => [
                        'showResults' => true,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testUpsertWithUpsertFailedException(): void
    {
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn([]);
        $this->questionnaireSettingsRepositoryMock->expects(self::once())
            ->method('persist')
            ->willThrowException(new UpsertFailedException('test'));
        try {
            $this->upserter->upsert(false);
            $this->fail('UpsertFailedException was expected to be thrown.');
        } catch (UpsertFailedException $exception) {
            self::assertSame('Upsert entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireSettings" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start upserting questionnaire settings.',
                    'context' => [
                        'showResults' => false,
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Upserting questionnaire settings was not successful.',
                    'context' => [
                        'showResults' => false,
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Upserting questionnaire settings was not successful.',
                    'context' => [
                        'showResults' => false,
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
