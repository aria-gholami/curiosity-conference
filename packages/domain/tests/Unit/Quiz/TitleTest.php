<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\Title;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Title::class)]
final class TitleTest extends TestCase
{
    public function testTitleIsValid(): void
    {
        $title = new Title('test');

        self::assertSame('test', $title->value);
    }

    public function testTitleWithEmptyStringException(): void
    {
        try {
            new Title('');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "quizTitle" with error: "Value for "quizTitle" should not be empty."', $exception->getMessage());
        }
    }
}
