<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PersisterMock;
use CuriosityConference\Application\UseCase\User\CreateHandler;
use CuriosityConference\Domain\User\Model\Id;
use CuriosityConference\Domain\User\Model\UserName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateHandler::class)]
#[UsesClass(UserName::class)]
#[UsesClass(Id::class)]
final class CreateHandlerTest extends TestCase
{
    private PersisterMock $persisterMock;
    private CreateHandler $createHandler;

    protected function setUp(): void
    {
        $this->persisterMock = new PersisterMock();
        $this->createHandler = new CreateHandler(
            $this->persisterMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $expectedUserId = 'test';
        $this->persisterMock->outputUserId = $expectedUserId;

        $userName = new UserName(
            'test',
        );

        $userId = $this->createHandler->handle($userName);

        self::assertSame($expectedUserId, $userId->value);
        self::assertSame('test', $this->persisterMock->inputUserName->value);
    }
}
