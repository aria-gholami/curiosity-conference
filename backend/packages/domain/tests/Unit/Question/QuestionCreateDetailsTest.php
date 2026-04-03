<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Question;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidNumberException;
use CuriosityConference\Domain\Question\QuestionCreateDetails;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuestionCreateDetails::class)]
final class QuestionCreateDetailsTest extends TestCase
{
    public function testQuestionCreateDetailsIsValid(): void
    {
        $questionCreateDetails = new QuestionCreateDetails(
            'test',
            'test',
            'test',
            'test',
            'test',
            'test',
            0,
        );

        self::assertSame('test', $questionCreateDetails->question);
        self::assertSame('test', $questionCreateDetails->quizId);
        self::assertSame('test', $questionCreateDetails->answer1);
        self::assertSame('test', $questionCreateDetails->answer2);
        self::assertSame('test', $questionCreateDetails->answer3);
        self::assertSame('test', $questionCreateDetails->answer4);
        self::assertSame(0, $questionCreateDetails->correctAnswerIndex);
    }

    public function testQuestionCreateDetailsWithCorrectAnswerIndex(): void
    {
        try {
            new QuestionCreateDetails(
                'test',
                'test',
                'test',
                'test',
                'test',
                'test',
                -1,
            );
            $this->fail('InvalidNumberException was expected to be thrown.');
        } catch (InvalidNumberException $exception) {
            self::assertSame('Validation failed for value "questionCreateDetailsCorrectAnswerIndex" with error: "Value for "questionCreateDetailsCorrectAnswerIndex" should not be -1."', $exception->getMessage());
        }
    }

    #[DataProvider('provideInvalidData')]
    public function testQuestionCreateDetailsWithEmptyStringException(array $payload, string $missingFieldName): void
    {
        try {
            new QuestionCreateDetails(
                $payload['question'],
                $payload['quizId'],
                $payload['answer1'],
                $payload['answer2'],
                $payload['answer3'],
                $payload['answer4'],
                $payload['correctAnswerIndex'],
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideInvalidData(): iterable
    {
        $payload = [
            'question' => 'test',
            'quizId' => 'test',
            'answer1' => 'test',
            'answer2' => 'test',
            'answer3' => 'test',
            'answer4' => 'test',
            'correctAnswerIndex' => 0,
        ];

        yield 'empty question' => [\array_replace($payload, ['question' => '']), 'questionCreateDetailsQuestion'];
        yield 'empty quizId' => [\array_replace($payload, ['quizId' => '']), 'questionCreateDetailsQuizId'];
        yield 'empty answer1' => [\array_replace($payload, ['answer1' => '']), 'questionCreateDetailsAnswer1'];
        yield 'empty answer2' => [\array_replace($payload, ['answer2' => '']), 'questionCreateDetailsAnswer2'];
        yield 'empty answer3' => [\array_replace($payload, ['answer3' => '']), 'questionCreateDetailsAnswer3'];
        yield 'empty answer4' => [\array_replace($payload, ['answer4' => '']), 'questionCreateDetailsAnswer4'];
    }
}
