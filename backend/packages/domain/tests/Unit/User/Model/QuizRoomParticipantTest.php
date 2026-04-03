<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\QuizRoomParticipant;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(QuizRoomParticipant::class)]
final class QuizRoomParticipantTest extends TestCase
{
    public function testQuizRoomParticipantIsValid(): void
    {
        $userDetails = new QuizRoomParticipant(
            'test',
            'test',
        );

        self::assertSame('test', $userDetails->participantId);
        self::assertSame('test', $userDetails->quizRoomId);
    }

    #[DataProvider('provideEmptyData')]
    public function testQuizRoomParticipantWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new QuizRoomParticipant(
                $payload['quizRoomId'] ?? '',
                $payload['participantId'] ?? '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideEmptyData(): iterable
    {
        $payload = [
            'quizRoomId' => 'test',
            'participantId' => 'test',
        ];

        yield 'empty quizRoomId' => [\array_replace($payload, ['quizRoomId' => '']), 'quizRoomParticipantQuizRoomId'];
        yield 'empty participantId' => [\array_replace($payload, ['participantId' => '']), 'quizRoomParticipantId'];
    }
}
