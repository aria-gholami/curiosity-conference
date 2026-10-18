<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\QuestionnaireQuestion;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;
use CuriosityConference\Domain\QuestionnaireQuestion\Question;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Question::class)]
final class QuestionTest extends TestCase
{
    public function testQuestionIsValid(): void
    {
        $question = new Question(
            'test',
            [
                '1',
                '2',
                '3',
                '4',
            ],
        );

        self::assertSame('test', $question->title);
        self::assertSame('1', $question->answers[0]);
        self::assertSame('2', $question->answers[1]);
        self::assertSame('3', $question->answers[2]);
        self::assertSame('4', $question->answers[3]);
    }

    public function testQuestionWithEmptyStringException(): void
    {
        try {
            new Question(
                '',
                [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "questionnaireQuestionTitle" with error: "Value for "questionnaireQuestionTitle" should not be empty."', $exception->getMessage());
        }
    }

    public function testQuestionWithEmptyAnswerArrayException(): void
    {
        try {
            new Question(
                'test',
                [],
            );
            $this->fail('EmptyAnswerArrayException was expected to be thrown.');
        } catch (EmptyAnswerArrayException $exception) {
            self::assertSame('Validation failed for value "questionnaireQuestionAnswers" with error: "Value for "questionnaireQuestionAnswers" should not be empty."', $exception->getMessage());
        }
    }
}
