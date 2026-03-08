<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Outbound\User;

use CuriosityConference\Adapters\Outbound\Persistence\Repository\UserRepository;
use CuriosityConference\Adapters\Outbound\User\DetailsUpdater;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(DetailsUpdater::class)]
#[UsesClass(UserDetails::class)]
final class DetailsUpdaterTest extends TestCase
{
    private MockObject $userEntityRepositoryMock;
    private LoggerMock $loggerMock;
    private DetailsUpdater $detailsUpdater;

    protected function setUp(): void
    {
        $this->userEntityRepositoryMock = $this->createMock(UserRepository::class);
        $this->loggerMock = new LoggerMock();
        $this->detailsUpdater = new DetailsUpdater($this->userEntityRepositoryMock, $this->loggerMock);
    }

    public function testUpdateIsValid(): void
    {
        $userDetails = new UserDetails(
            'test',
            'test',
            'test',
        );
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('updateNameById')
            ->with($userDetails);

        $this->detailsUpdater->updateUserDetails($userDetails);

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating user details.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
                [
                    'message' => 'Updating user details was successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testLoadWithLoadEntityFailedException(): void
    {
        $userDetails = new UserDetails(
            'test',
            'test',
            'test',
        );
        $this->userEntityRepositoryMock->expects($this->once())
            ->method('updateNameById')
            ->with($userDetails)
            ->willThrowException(new \Exception('test'));
        try {
            $this->detailsUpdater->updateUserDetails($userDetails);
            $this->fail('LoadEntityFailedException was expected to be thrown.');
        } catch (UpdateFailedException $exception) {
            self::assertSame('Update entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.', $exception->getMessage());
        }

        self::assertEquals([
            'info' => [
                [
                    'message' => 'Start updating user details.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
            'notice' => [
                [
                    'message' => 'Updating user details was not successful.',
                    'context' => [
                        'userId' => 'test',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Updating user details was not successful.',
                    'context' => [
                        'userId' => 'test',
                        'exceptionMessage' => 'test',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
