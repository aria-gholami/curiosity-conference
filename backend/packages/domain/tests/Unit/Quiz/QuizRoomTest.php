<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\QuizRoom;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizRoom::class)]
final class QuizRoomTest extends TestCase
{
    public function testQuizRoomIsValid(): void
    {
        $quizRoom = new QuizRoom('test', 'test', 'test');

        self::assertSame('test', $quizRoom->quizId);
        self::assertSame('test', $quizRoom->hostId);
    }

    public function testQuizRoomQuizIdWithEmptyStringException(): void
    {
        try {
            new QuizRoom('', 'test', 'test');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "quizRoomQuizId" with error: "Value for "quizRoomQuizId" should not be empty."', $exception->getMessage());
        }
    }

    public function testQuizRoomHostIdWithEmptyStringException(): void
    {
        try {
            new QuizRoom('test', '', 'test');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "quizRoomHostId" with error: "Value for "quizRoomHostId" should not be empty."', $exception->getMessage());
        }
    }

    public function testQuizRoomTitleWithEmptyStringException(): void
    {
        try {
            new QuizRoom('test', 'test', '');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "quizRoomHostTitle" with error: "Value for "quizRoomHostTitle" should not be empty."', $exception->getMessage());
        }
    }
}
