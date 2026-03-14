<?php

declare(strict_types=1);

namespace CuriosityConference\Domain\Tests\Unit\Device;

use CuriosityConference\Domain\Device\DeviceRegistration;
use CuriosityConference\Domain\Exception\EmptyStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceRegistration::class)]
final class DeviceRegistrationTest extends TestCase
{
    public function testDeviceIsValid(): void
    {
        $deviceRegistration = new DeviceRegistration(
            'test',
            'test',
        );

        self::assertSame('test', $deviceRegistration->registrationToken);
        self::assertSame('test', $deviceRegistration->registrationToken);
    }

    #[DataProvider('provideInvalidData')]
    public function testDeviceRegistrationWithEmptyStringException(array $payload, string $identifier): void
    {
        try {
            new DeviceRegistration($payload['registrationToken'], $payload['userId']);
            $this->fail('EmptyStringException was expected to be thrown.');
        } catch (EmptyStringException $exception) {
            self::assertSame(EmptyStringException::stringIsEmpty($identifier)->getMessage(), $exception->getMessage());
        }
    }

    public static function provideInvalidData(): iterable
    {
        $payload = [
            'registrationToken' => 'test',
            'userId' => 'test',
        ];

        yield 'invalid registrationToken' => [\array_replace($payload, ['registrationToken' => '']), 'deviceRegistrationToken'];
        yield 'invalid userId' => [\array_replace($payload, ['userId' => '']), 'deviceRegistrationUserId'];
    }
}
