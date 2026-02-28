<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Quiz;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Quiz\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Id::class)]
final class IdTest extends TestCase
{
    public function testTitleIsValid(): void
    {
        $id = new Id('test');

        self::assertSame('test', $id->value);
    }

    public function testTitleWithEmptyStringException(): void
    {
        try {
            new Id('');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "id" with error: "Value for "id" should not be empty."', $exception->getMessage());
        }
    }
}
