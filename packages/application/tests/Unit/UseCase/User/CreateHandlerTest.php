<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Application\Tests\Unit\Mocks\Argon2idHasherMock;
use CuriosityConference\Application\Tests\Unit\Mocks\JwtUserTokenMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\PersisterMock;
use CuriosityConference\Application\UseCase\User\CreateHandler;
use CuriosityConference\Domain\User\Model\UserRegistration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateHandler::class)]
#[UsesClass(UserRegistration::class)]
final class CreateHandlerTest extends TestCase
{
    private PersisterMock $persisterMock;
    private Argon2idHasherMock $argon2idHasherMock;
    private JwtUserTokenMock $jwtUserTokenMock;
    private CreateHandler $createHandler;

    protected function setUp(): void
    {
        $this->persisterMock = new PersisterMock();
        $this->argon2idHasherMock = new Argon2idHasherMock();
        $this->jwtUserTokenMock = new JwtUserTokenMock();
        $this->createHandler = new CreateHandler(
            $this->persisterMock,
            $this->argon2idHasherMock,
            $this->jwtUserTokenMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $expectedUserId = 'test';
        $this->persisterMock->outputUserId = $expectedUserId;
        $expectedHashedPassword = '133243546456';
        $this->argon2idHasherMock->outputValue = $expectedHashedPassword;
        $this->jwtUserTokenMock->outputJwtToken = 'test';

        $user = new UserRegistration(
            'test',
            'test',
            'test@web.de',
            'Test123!',
            'Test123!',
        );

        $userId = $this->createHandler->handle($user);

        self::assertSame($expectedUserId, $userId);
        self::assertSame($user->password, $this->argon2idHasherMock->inputValue);
        self::assertSame($user->firstName, $this->persisterMock->inputUser->firstName);
        self::assertSame($user->lastName, $this->persisterMock->inputUser->lastName);
        self::assertSame($user->email, $this->persisterMock->inputUser->email);
        self::assertSame($expectedHashedPassword, $this->persisterMock->inputUser->password);
        self::assertSame($user->firstName, $this->jwtUserTokenMock->inputUser->getFirstName());
        self::assertSame($user->lastName, $this->jwtUserTokenMock->inputUser->getLastName());
        self::assertSame($user->email, $this->jwtUserTokenMock->inputUser->getEmail());
    }
}
