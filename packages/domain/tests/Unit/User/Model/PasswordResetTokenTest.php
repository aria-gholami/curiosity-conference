<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Exception\InvalidPasswordResetTokenException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetToken::class)]
final class PasswordResetTokenTest extends TestCase
{
    #[DataProvider('provideValidToken')]
    public function testPasswordResetTokenIsValid(array $payload): void
    {
        $passwordResetToken = new PasswordResetToken(
            $payload['token'],
            'test',
        );

        self::assertSame($payload['token'], $passwordResetToken->token);
        self::assertSame('test', $passwordResetToken->email);
    }

    public function testPasswordResetTokenWithEmptyStringException(): void
    {
        try {
            new PasswordResetToken(
                12345678,
                '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame('Validation failed for value "passwordResetTokenUserEmail" with error: "Value for "passwordResetTokenUserEmail" should not be empty."', $exception->getMessage());
        }
    }

    #[DataProvider('provideInvalidToken')]
    public function testPasswordResetTokenWithInvalidPasswordResetTokenException($payload): void
    {
        try {
            new PasswordResetToken(
                $payload['token'],
                'test',
            );
            $this->fail('InvalidPasswordResetTokenException was expected to be thrown.');
        } catch (InvalidPasswordResetTokenException $exception) {
            self::assertSame('Validation failed for value "passwordReset" with error: "Token passwordReset must be exactly 8 digits."', $exception->getMessage());
        }
    }

    public static function provideInvalidToken(): iterable
    {
        $payload = [
            'token' => 12345678,
        ];

        yield '1 number' => [\array_replace($payload, ['token' => 1])];
        yield '9 numbers' => [\array_replace($payload, ['token' => 123456789])];
    }

    public static function provideValidToken(): iterable
    {
        $payload = [
            'token' => 12345678,
        ];

        yield 'with value 12345678' => [\array_replace($payload, ['token' => 12345678])];
        yield 'with value 10000000' => [\array_replace($payload, ['token' => 10000000])];
        yield 'with value 99999999' => [\array_replace($payload, ['token' => 99999999])];
    }
}
