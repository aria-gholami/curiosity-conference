<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Device;

use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\EmptyStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistrationToken::class)]
final class RegistrationTokenTest extends TestCase
{
    public function testRegistrationTokenIsValid(): void
    {
        $registrationToken = new RegistrationToken(
            'test',
        );

        self::assertSame('test', $registrationToken->value);
    }

    public function testRegistrationTokenWithEmptyStringException(): void
    {
        try {
            new RegistrationToken('');
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "registrationToken" with error: "Value for "registrationToken" should not be empty."', $exception->getMessage());
        }
    }
}
