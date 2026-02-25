<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\User\Model\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
final class UserTest extends TestCase
{
    public function testUserIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test@web.de',
            'Test123!',
            'Test123!',
        );

        self::assertSame('test', $user->firstName);
        self::assertSame('test', $user->lastName);
        self::assertSame('test@web.de', $user->email);
        self::assertSame('Test123!', $user->password);
        self::assertSame('Test123!', $user->confirmPassword);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new User(
                $payload['firstName'] ?? '',
                $payload['lastName'] ?? '',
                $payload['email'] ?? '',
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
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
        ];

        yield 'empty firstName' => [\array_replace($payload, ['firstName' => '']), 'userFirstName'];
        yield 'empty lastName' => [\array_replace($payload, ['lastName' => '']), 'userLastName'];
        yield 'empty email' => [\array_replace($payload, ['email' => '']), 'userEmail'];
        yield 'empty password' => [\array_replace($payload, ['password' => '']), 'userPassword'];
        yield 'empty confirm password' => [\array_replace($payload, ['confirmPassword' => '']), 'userConfirmPassword'];
    }

    public function testUserWithInvalidEmailRegex(): void
    {
        try {
            new User(
                'test',
                'test',
                'test',
                'Test123!',
                'Test123!',
            );
            $this->fail('InvalidEmailException was expected to be thrown.');
        } catch (InvalidEmailException $exception) {
            self::assertSame('Validation failed for value "user" with error: "Email from "user" is invalid."', $exception->getMessage());
        }
    }

    public function testUserWithPasswordNotSameAsConfirmPassword(): void
    {
        try {
            new User(
                'test',
                'test',
                'test',
                'Test123!',
                'Test124!',
            );
            $this->fail('InvalidEmailException was expected to be thrown.');
        } catch (InvalidEmailException $exception) {
            self::assertSame('Validation failed for value "user" with error: "Email from "user" is invalid."', $exception->getMessage());
        }
    }

    public function testUserWithInvalidPassword(): void
    {
        try {
            new User(
                'test',
                'test',
                'test',
                'Test123!',
                'Test123',
            );
            $this->fail('InvalidEmailException was expected to be thrown.');
        } catch (InvalidEmailException $exception) {
            self::assertSame('Validation failed for value "user" with error: "Email from "user" is invalid."', $exception->getMessage());
        }
    }
}
