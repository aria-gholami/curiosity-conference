<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JoinQuizRoom::class)]
final class JoinQuizRoomTest extends TestCase
{
    public function testJoinQuizRoomIsValid(): void
    {
        $joinQuizRoom = new JoinQuizRoom('test', 'test');

        self::assertSame('test', $joinQuizRoom->quizRoomId);
        self::assertSame('test', $joinQuizRoom->userId);
    }

    public function testJoinQuizRoomQuizRoomIdWithEmptyStringException(): void
    {
        try {
            new JoinQuizRoom('', 'test');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "joinQuizRoomId" with error: "Value for "joinQuizRoomId" should not be empty."', $exception->getMessage());
        }
    }

    public function testJoinQuizRoomUserIdWithEmptyStringException(): void
    {
        try {
            new JoinQuizRoom('test', '');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "joinQuizRoomUserId" with error: "Value for "joinQuizRoomUserId" should not be empty."', $exception->getMessage());
        }
    }
}
