<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\QuestionnaireQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireQuestionRepository;
use CuriosityConference\Adapters\Outbound\QuestionnaireQuestion\QuestionResetter;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuestionResetter::class)]
final class QuestionResetterTest extends TestCase
{
    private MockObject $questionnaireQuestionRepositoryMock;
    private LoggerMock $loggerMock;
    private QuestionResetter $questionResetter;

    protected function setUp(): void
    {
        $this->questionnaireQuestionRepositoryMock = $this->createMock(QuestionnaireQuestionRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->questionResetter = new QuestionResetter(
            $this->questionnaireQuestionRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testResetQuestionsIsValid(): void
    {
        $this->questionnaireQuestionRepositoryMock->expects(self::once())
            ->method('reset');
        $this->questionResetter->reset();

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start resetting questionnaire questions.',
                    'context' => [],
                ],
                [
                    'message' => 'Resetting questionnaire questions was successful.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testResetQuestionsWithDeleteFailedException(): void
    {
        $this->questionnaireQuestionRepositoryMock->expects(self::once())
            ->method('reset')
            ->willThrowException(new DeleteFailedException('test'));
        try {
            $this->questionResetter->reset();
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start resetting questionnaire questions.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Resetting questionnaire questions was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Resetting questionnaire questions was not successful.',
                    'context' => [
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
