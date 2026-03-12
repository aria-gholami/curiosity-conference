<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Id::class)]
final class IdTest extends TestCase
{
    public function testIdIsValid(): void
    {
        $id = new Id(
            'test',
        );

        self::assertSame('test', $id->value);
    }

    public function testIdWithEmptyStringException(): void
    {
        try {
            new Id('');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "userId" with error: "Value for "userId" should not be empty."', $exception->getMessage());
        }
    }
}
