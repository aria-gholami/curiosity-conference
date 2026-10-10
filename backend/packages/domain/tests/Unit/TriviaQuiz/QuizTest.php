<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\TriviaQuiz\Quiz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Quiz::class)]
final class QuizTest extends TestCase
{
    public function testQuizIsValid(): void
    {
        $quiz = new Quiz(
            'test',
        );

        self::assertSame('test', $quiz->title);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new Quiz(
                $payload['title'] ?? '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideEmptyData(): iterable
    {
        $payload = [
            'title' => 'test',
        ];

        yield 'empty title' => [\array_replace($payload, ['title' => '']), 'quizTitle'];
    }
}
