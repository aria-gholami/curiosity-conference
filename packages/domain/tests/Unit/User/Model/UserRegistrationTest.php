<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\User\Model\UserRegistration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserRegistration::class)]
final class UserRegistrationTest extends TestCase
{
    public function testUserIsValid(): void
    {
        $userRegistration = new UserRegistration(
            'test',
            'test',
            'test@web.de',
            'Test123!',
            'Test123!',
        );

        self::assertSame('test', $userRegistration->firstName);
        self::assertSame('test', $userRegistration->lastName);
        self::assertSame('test@web.de', $userRegistration->email);
        self::assertSame('Test123!', $userRegistration->password);
        self::assertSame('Test123!', $userRegistration->confirmPassword);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new UserRegistration(
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

        yield 'empty firstName' => [\array_replace($payload, ['firstName' => '']), 'userRegistrationFirstName'];
        yield 'empty lastName' => [\array_replace($payload, ['lastName' => '']), 'userRegistrationLastName'];
        yield 'empty email' => [\array_replace($payload, ['email' => '']), 'userRegistrationEmail'];
        yield 'empty password' => [\array_replace($payload, ['password' => '']), 'userRegistrationPassword'];
        yield 'empty confirm password' => [\array_replace($payload, ['confirmPassword' => '']), 'userRegistrationConfirmPassword'];
    }

    public function testUserWithInvalidEmailRegex(): void
    {
        try {
            new UserRegistration(
                'test',
                'test',
                'test',
                'Test123!',
                'Test123!',
            );
            $this->fail('InvalidEmailException was expected to be thrown.');
        } catch (InvalidEmailException $exception) {
            self::assertSame('Validation failed for value "userRegistration" with error: "Email from "userRegistration" is invalid."', $exception->getMessage());
        }
    }

    public function testUserWithPasswordNotSameAsConfirmPassword(): void
    {
        try {
            new UserRegistration(
                'test',
                'test',
                'test@web.de',
                'Test123!',
                'Test124!',
            );
            $this->fail('InvalidPasswordException was expected to be thrown.');
        } catch (InvalidPasswordException $exception) {
            self::assertSame('Validation failed for value "userRegistrationConfirmPassword" with error: "Password is not the same as confirmPassword."', $exception->getMessage());
        }
    }

    public function testUserWithInvalidPassword(): void
    {
        try {
            new UserRegistration(
                'test',
                'test',
                'test@web.de',
                'Test123',
                'Test123',
            );
            $this->fail('InvalidPasswordException was expected to be thrown.');
        } catch (InvalidPasswordException $exception) {
            self::assertSame('Validation failed for value "userRegistrationPassword" with error: "Password "Test123" is not valid: ."', $exception->getMessage());
        }
    }
}
