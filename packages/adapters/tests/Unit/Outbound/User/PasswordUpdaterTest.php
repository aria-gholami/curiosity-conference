<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\PasswordUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\Argon2idHasherMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordUpdater::class)]
#[UsesClass(UserPassword::class)]
final class PasswordUpdaterTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private Argon2idHasherMock $argon2idHasherMock;
    private LoggerMock $loggerMock;
    private PasswordUpdater $passwordUpdater;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->argon2idHasherMock = new Argon2idHasherMock();
        $this->loggerMock = new LoggerMock();
        $this->passwordUpdater = new PasswordUpdater($this->userEntityRepositoryMock, $this->argon2idHasherMock, $this->loggerMock);
    }

    public function testUpdateIsValid(): void
    {
        $userPassword = new UserPassword(
            'test',
            'Test123!',
            'Test123!',
        );
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('updatePasswordByEmail')
            ->with($userPassword);
        $this->argon2idHasherMock->outputValue = 'Test123!';

        $this->passwordUpdater->update($userPassword);

        self::assertSame($userPassword->password, $this->argon2idHasherMock->inputValue);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating user password.',
                    'context' => [
                        'userEmail' => 'test',
                    ],
                ],
                [
                    'message' => 'Updating user password was successful.',
                    'context' => [
                        'userEmail' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $userPassword = new UserPassword(
            'test',
            'Test123!',
            'Test123!',
        );
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('updatePasswordByEmail')
            ->with($userPassword)
            ->willThrowException(new \Exception('test'));
        $this->argon2idHasherMock->outputValue = 'Test123!';

        try {
            $this->passwordUpdater->update($userPassword);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating user password.',
                    'context' => [
                        'userEmail' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating user password was not successful.',
                    'context' => [
                        'userEmail' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating user password was not successful.',
                    'context' => [
                        'userEmail' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
