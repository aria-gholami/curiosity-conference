<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\Device;

use CuriosityConference\Adapters\Outbound\Device\ByRegistrationTokenDeleter;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\Device;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\DeviceRepository;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByRegistrationTokenDeleter::class)]
#[UsesClass(RegistrationToken::class)]
final class ByRegistrationTokenDeleterTest extends TestCase
{
    private MockObject $deviceEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByRegistrationTokenDeleter $byRegistrationTokenDeleter;

    protected function setUp(): void
    {
        $this->deviceEntityRepositoryMock = $this->createMock(DeviceRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byRegistrationTokenDeleter = new ByRegistrationTokenDeleter(
            $this->deviceEntityRepositoryMock,
            $this->loggerMock,
        );
    }

    public function testDeleteIsValid(): void
    {
        $registrationToken = new RegistrationToken(
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $device = new Device(
            'test',
            'test',
            $user,
        );
        $this->deviceEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['registrationToken' => $registrationToken->value])
            ->willReturn($device);
        $this->deviceEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->with($device);

        $this->byRegistrationTokenDeleter->delete($registrationToken);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting device.',
                    'context' => [
                        'registrationToken' => 'test',
                    ],
                ],
                [
                    'message' => 'Deleting device was successful.',
                    'context' => [
                        'registrationToken' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithNoDeviceFound(): void
    {
        $registrationToken = new RegistrationToken(
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $device = new Device(
            'test',
            'test',
            $user,
        );
        $this->deviceEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['registrationToken' => $registrationToken->value])
            ->willReturn(null);
        $this->deviceEntityRepositoryMock->expects(self::never())
            ->method('remove')
            ->with($device);

        $this->byRegistrationTokenDeleter->delete($registrationToken);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting device.',
                    'context' => [
                        'registrationToken' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testDeleteWithDeleteFailedException(): void
    {
        $registrationToken = new RegistrationToken(
            'test',
        );
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
        );
        $device = new Device(
            'test',
            'test',
            $user,
        );
        $this->deviceEntityRepositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['registrationToken' => $registrationToken->value])
            ->willReturn($device);
        $this->deviceEntityRepositoryMock->expects(self::once())
            ->method('remove')
            ->willThrowException(new DeleteFailedException('test'));

        try {
            $this->byRegistrationTokenDeleter->delete($registrationToken);
            $this->fail('DeleteFailedException was expected to be thrown.');
        } catch (DeleteFailedException $exception) {
            self::assertSame('Delete entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\Device" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start deleting device.',
                    'context' => [
                        'registrationToken' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Deleting device was not successful.',
                    'context' => [
                        'registrationToken' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Deleting device was not successful.',
                    'context' => [
                        'registrationToken' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
