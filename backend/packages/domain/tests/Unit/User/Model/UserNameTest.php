<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\User\Exception\InvalidNameException;
use CuriosityConference\Domain\User\Model\UserName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserName::class)]
final class UserNameTest extends TestCase
{
    public function testUserNameIsValid(): void
    {
        $userName = new UserName(
            'test',
        );

        self::assertSame('test', $userName->value);
    }

    public function testUserNameWithEmptyValue(): void
    {
        try {
            new UserName(
                '',
            );
            $this->fail('InvalidNameException was expected to be thrown.');
        } catch (InvalidNameException $exception) {
            self::assertSame(InvalidNameException::isInvalid('userName')->getMessage(), $exception->getMessage());
        }
    }

    public function testUserNameWith20Chars(): void
    {
        try {
            new UserName(
                '12345678901234567890',
            );
            $this->fail('InvalidNameException was expected to be thrown.');
        } catch (InvalidNameException $exception) {
            self::assertSame(InvalidNameException::isInvalid('userName')->getMessage(), $exception->getMessage());
        }
    }

    public function testUserNameWith15Chars(): void
    {
        $userName = new UserName(
            '123456789012345',
        );

        self::assertSame('123456789012345', $userName->value);
    }

    public function testUserNameWithUmlaute(): void
    {
        $userName = new UserName(
            'äääääääääääääää',
        );

        self::assertSame('äääääääääääääää', $userName->value);
    }
}
