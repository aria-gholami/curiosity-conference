<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(HighscoreUpdateRequestBody::class)]
final class ContinueOrFinishRequestBodyTest extends TestCase
{
    public function testContinueOrFinishRequestBodyIsValid(): void
    {
        $continueOrFinishRequestBody = new HighscoreUpdateRequestBody(
            'test',
            'test',
            'test',
        );

        self::assertSame('test', $continueOrFinishRequestBody->quizRoomId);
        self::assertSame('test', $continueOrFinishRequestBody->userId);
        self::assertSame('test', $continueOrFinishRequestBody->answerId);
    }

    #[DataProvider('provideEmptyData')]
    public function testContinueOrFinishRequestBodyWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new HighscoreUpdateRequestBody(
                $payload['quizRoomId'] ?? '',
                $payload['userId'] ?? '',
                $payload['answerId'] ?? '',
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
            'userId' => 'test',
            'answerId' => 'test',
        ];

        yield 'empty quizRoomId' => [\array_replace($payload, ['quizRoomId' => '']), 'continueOrFinishRequestBodyQuizRoomId'];
        yield 'empty userId' => [\array_replace($payload, ['userId' => '']), 'continueOrFinishRequestBodyUserId'];
        yield 'empty answerId' => [\array_replace($payload, ['answerId' => '']), 'continueOrFinishRequestBodyAnswerId'];
    }
}
