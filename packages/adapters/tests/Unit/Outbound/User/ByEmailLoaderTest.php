<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\ByEmailLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByEmailLoader::class)]
final class ByEmailLoaderTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByEmailLoader $byEmailLoader;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byEmailLoader = new ByEmailLoader($this->userEntityRepositoryMock, $this->loggerMock);
    }

    public function testLoadIsValid(): void
    {
        $outputUser = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@email.de'])
            ->willReturn($outputUser);
        $email = 'test@email.de';

        $user = $this->byEmailLoader->load($email);

        self::assertSame($outputUser->getEmail(), $user->getEmail());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [],
                ],
                [
                    'message' => 'Loading user was successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [
                        'userEmail' => 'test@email.de',
                    ],
                ],
                [
                    'message' => 'Loading user was successful.',
                    'context' => [
                        'userEmail' => 'test@email.de',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@email.de'])
            ->willReturn(null);
        $email = 'test@email.de';

        try {
            $this->byEmailLoader->load($email);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading user was not successful.',
                    'context' => [],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [
                        'userEmail' => 'test@email.de',
                    ],
                ],
                [
                    'message' => 'Loading user was not successful.',
                    'context' => [
                        'userEmail' => 'test@email.de',
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
