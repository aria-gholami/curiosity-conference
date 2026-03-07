<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\ByIdLoader;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ByIdLoader::class)]
final class ByIdLoaderTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private ByIdLoader $byIdLoader;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->byIdLoader = new ByIdLoader($this->userEntityRepositoryMock, $this->loggerMock);
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
            ->with(['id' => 'test'])
            ->willReturn($outputUser);
        $id = 'test';

        $user = $this->byIdLoader->load($id);

        self::assertSame($outputUser->getEmail(), $user->getEmail());
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
                [
                    'message' => 'Loading user was successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 'test'])
            ->willReturn(null);
        $id = 'test';

        try {
            $this->byIdLoader->load($id);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (LoadEntityFailedException $exception) {
            self::assertSame('Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start loading user.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Loading user was not successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Loading user was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
