<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\QuestionnaireAnswer;

use CuriosityConference\Adapters\Outbound\QuestionnaireAnswer\Persister;
use CuriosityConference\Adapters\Tests\Unit\Mocks\EntityManagerMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Persister::class)]
#[UsesClass(Question::class)]
final class PersisterTest extends TestCase
{
    private EntityManagerMock $questionnaireAnswerEntityManagerMock;
    private LoggerMock $loggerMock;
    private Persister $persister;

    protected function setUp(): void
    {
        $this->questionnaireAnswerEntityManagerMock = new EntityManagerMock();
        $this->loggerMock = new LoggerMock();
        $this->persister = new Persister(
            $this->questionnaireAnswerEntityManagerMock,
            $this->loggerMock,
        );
    }

    public function testPersistIsValid(): void
    {
        $question = new Question('test', ['1', '2', '3', '4']);

        $this->persister->persist($question);

        self::assertTrue($this->questionnaireAnswerEntityManagerMock->isFlushCalled);
        self::assertSame('test', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[0]->getTitle());
        self::assertSame('1', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[1]->getTitle());
        self::assertSame('test', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[1]->getQuestionnaireQuestion()->getTitle());
        self::assertSame('2', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[2]->getTitle());
        self::assertSame('test', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[2]->getQuestionnaireQuestion()->getTitle());
        self::assertSame('3', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[3]->getTitle());
        self::assertSame('test', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[3]->getQuestionnaireQuestion()->getTitle());
        self::assertSame('4', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[4]->getTitle());
        self::assertSame('test', $this->questionnaireAnswerEntityManagerMock->inputPersistedObject[4]->getQuestionnaireQuestion()->getTitle());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting questionnaire question.',
                    'context' => [
                        'questionnaireQuestionTitle' => 'test',
                    ],
                ],
                [
                    'message' => 'Persisting questionnaire question was successful.',
                    'context' => [
                        'questionnaireQuestionTitle' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testPersistWithPersistFailedException(): void
    {
        $this->questionnaireAnswerEntityManagerMock->throwPersistException = new PersistFailedException('test');
        $question = new Question('test', ['1', '2', '3', '4']);

        try {
            $this->persister->persist($question);
            $this->fail('PersistFailedException was expected to be thrown.');
        } catch (PersistFailedException $exception) {
            self::assertSame('Persist entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer" was not successful.', $exception->getMessage());
        }

        self::assertFalse($this->questionnaireAnswerEntityManagerMock->isFlushCalled);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start persisting questionnaire question.',
                    'context' => [
                        'questionnaireQuestionTitle' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Persisting questionnaire question was not successful.',
                    'context' => [
                        'questionnaireQuestionTitle' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Persisting questionnaire question was not successful.',
                    'context' => [
                        'questionnaireQuestionTitle' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
