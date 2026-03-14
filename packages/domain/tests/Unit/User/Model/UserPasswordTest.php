<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\User\Model\UserPassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserPassword::class)]
final class UserPasswordTest extends TestCase
{
    public function testUserIsValid(): void
    {
        $userPassword = new UserPassword(
            'test',
            'Test123!',
            'Test123!',
        );

        self::assertSame('test', $userPassword->email);
        self::assertSame('Test123!', $userPassword->password);
        self::assertSame('Test123!', $userPassword->confirmPassword);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new UserPassword(
                $payload['id'] ?? '',
                $payload['password'] ?? '',
                $payload['confirmPassword'] ?? '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideEmptyData(): iterable
    {
        $payload = [
            'id' => 'test',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
        ];

        yield 'empty id' => [\array_replace($payload, ['id' => '']), 'userPasswordEmail'];
        yield 'empty password' => [\array_replace($payload, ['password' => '']), 'userPasswordPassword'];
        yield 'empty confirm password' => [\array_replace($payload, ['confirmPassword' => '']), 'userPasswordConfirmPassword'];
    }

    public function testUserWithPasswordNotSameAsConfirmPassword(): void
    {
        try {
            new UserPassword(
                'test',
                'Test123!',
                'Test124!',
            );
            $this->fail('InvalidPasswordException was expected to be thrown.');
        } catch (InvalidPasswordException $exception) {
            self::assertSame('Validation failed for value "userPasswordConfirmPassword" with error: "Password is not the same as confirmPassword."', $exception->getMessage());
        }
    }

    public function testUserWithInvalidPassword(): void
    {
        try {
            new UserPassword(
                'test',
                'Test123',
                'Test123',
            );
            $this->fail('InvalidPasswordException was expected to be thrown.');
        } catch (InvalidPasswordException $exception) {
            self::assertSame('Validation failed for value "userPasswordPassword" with error: "Password "Test123" is not valid: ."', $exception->getMessage());
        }
    }
}
