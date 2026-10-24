<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\TriviaQuiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\TriviaQuiz\Exception\InvalidRoomTitleException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoom;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizRoom::class)]
final class QuizRoomTest extends TestCase
{
    public function testQuizRoomIsValid(): void
    {
        $quizRoom = new QuizRoom('test', 'test');

        self::assertSame('test', $quizRoom->hostId);
        self::assertSame('test', $quizRoom->title);
    }

    public function testQuizRoomHostIdWithEmptyStringException(): void
    {
        try {
            new QuizRoom('', 'test');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "quizRoomHostId" with error: "Value for "quizRoomHostId" should not be empty."', $exception->getMessage());
        }
    }

    public function testQuizRoomTitleWithInvalidRoomTitleException(): void
    {
        try {
            new QuizRoom('test', '');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (InvalidRoomTitleException $exception) {
            self::assertSame('Validation failed for value "quizRoomTitle" with error: "Value for "quizRoomTitle" should not be valid."', $exception->getMessage());
        }
    }

    public function testQuizRoomTitleWith20Chars(): void
    {
        try {
            new QuizRoom(
                'test',
                '12345678901234567890',
            );
            $this->fail('InvalidNameException was expected to be thrown.');
        } catch (InvalidRoomTitleException $exception) {
            self::assertSame(InvalidRoomTitleException::isInvalid('quizRoomTitle')->getMessage(), $exception->getMessage());
        }
    }

    public function testQuizRoomTitleWith15Chars(): void
    {
        $quizRoom = new QuizRoom(
            'test',
            '123456789012345',
        );

        self::assertSame('test', $quizRoom->hostId);
        self::assertSame('123456789012345', $quizRoom->title);
    }

    public function testQuizRoomTitleWithUmlaute(): void
    {
        $quizRoom = new QuizRoom(
            'test',
            'äääääääääääääää',
        );

        self::assertSame('test', $quizRoom->hostId);
        self::assertSame('äääääääääääääää', $quizRoom->title);
    }
}
