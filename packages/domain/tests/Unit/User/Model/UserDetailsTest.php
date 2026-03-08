<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\User\Model;

use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\UserDetails;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserDetails::class)]
final class UserDetailsTest extends TestCase
{
    public function testUserIsValid(): void
    {
        $userDetails = new UserDetails(
            'test',
            'test',
            'test',
        );

        self::assertSame('test', $userDetails->id);
        self::assertSame('test', $userDetails->firstName);
        self::assertSame('test', $userDetails->lastName);
    }

    #[DataProvider('provideEmptyData')]
    public function testUserWithInvalidData(array $payload, string $missingFieldName): void
    {
        try {
            new UserDetails(
                $payload['id'] ?? '',
                $payload['firstName'] ?? '',
                $payload['lastName'] ?? '',
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
            'firstName' => 'test',
            'lastName' => 'test',
        ];

        yield 'empty id' => [\array_replace($payload, ['id' => '']), 'userDetailsId'];
        yield 'empty firstName' => [\array_replace($payload, ['firstName' => '']), 'userDetailsFirstName'];
        yield 'empty lastName' => [\array_replace($payload, ['lastName' => '']), 'userDetailsLastName'];
    }
}
