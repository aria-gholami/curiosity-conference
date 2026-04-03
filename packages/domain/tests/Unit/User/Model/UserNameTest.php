<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
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

    public function testUserNameWithInvalidData(): void
    {
        try {
            new UserName(
                '',
            );
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty('userName')->getMessage(), $exception->getMessage());
        }
    }
}
