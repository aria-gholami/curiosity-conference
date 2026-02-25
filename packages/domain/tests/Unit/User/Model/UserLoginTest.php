<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\UserLogin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserLogin::class)]
final class UserLoginTest extends TestCase
{
    public function testUserLoginIsValid(): void
    {
        $userLogin = new UserLogin(
            'test@test.de',
            'test',
        );

        self::assertSame('test@test.de', $userLogin->email);
        self::assertSame('test', $userLogin->password);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new UserLogin(
                $payload['email'] ?? '',
                $payload['password'] ?? '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($missingFieldName)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideEmptyData(): iterable
    {
        $payload = [
            'email' => 'test@web.de',
            'password' => 'Test123!',
        ];

        yield 'empty email' => [\array_replace($payload, ['email' => '']), 'userLoginEmail'];
        yield 'empty password' => [\array_replace($payload, ['password' => '']), 'userLoginPassword'];
    }
}
