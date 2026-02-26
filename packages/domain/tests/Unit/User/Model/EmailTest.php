<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Email::class)]
final class EmailTest extends TestCase
{
    public function testEmailIsValid(): void
    {
        $email = new Email(
            'test@test.de',
        );

        self::assertSame('test@test.de', $email->value);
    }

    public function testEmailWithEmptyStringException(): void
    {
        try {
            new Email('');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "userEmail" with error: "Value for "userEmail" should not be empty."', $exception->getMessage());
        }
    }
}
