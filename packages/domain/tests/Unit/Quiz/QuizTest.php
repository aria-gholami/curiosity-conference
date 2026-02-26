<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\Quiz;
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
            'test',
            'test',
        );

        self::assertSame('test', $quiz->title);
        self::assertSame('test', $quiz->description);
        self::assertSame('test', $quiz->category);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new Quiz(
                $payload['title'] ?? '',
                $payload['description'] ?? '',
                $payload['category'] ?? '',
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
            'description' => 'test',
            'category' => 'test',
        ];

        yield 'empty title' => [\array_replace($payload, ['title' => '']), 'quizTitle'];
        yield 'empty description' => [\array_replace($payload, ['description' => '']), 'quizDescription'];
        yield 'empty category' => [\array_replace($payload, ['category' => '']), 'quizCategory'];
    }
}
