<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\Argon2idHasherMock;
use CuriosityConference\Application\Tests\Unit\Mocks\JwtUserTokenMock;
use CuriosityConference\Application\Tests\Unit\UseCase\User\Mocks\ByEmailLoaderMock;
use CuriosityConference\Application\UseCase\User\LoginHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LoginHandler::class)]
#[UsesClass(UserLogin::class)]
final class LoginHandlerTest extends TestCase
{
    private ByEmailLoaderMock $byEmailLoaderMock;
    private Argon2idHasherMock $argon2idHasherMock;
    private JwtUserTokenMock $jwtUserTokenMock;
    private LoggerMock $loggerMock;
    private LoginHandler $loginHandler;

    protected function setUp(): void
    {
        $this->byEmailLoaderMock = new ByEmailLoaderMock();
        $this->argon2idHasherMock = new Argon2idHasherMock();
        $this->jwtUserTokenMock = new JwtUserTokenMock();
        $this->loggerMock = new LoggerMock();
        $this->loginHandler = new LoginHandler(
            $this->byEmailLoaderMock,
            $this->argon2idHasherMock,
            $this->jwtUserTokenMock,
            $this->loggerMock,
        );
    }

    public function testHandleIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->argon2idHasherMock->outputVerified = true;
        $this->jwtUserTokenMock->outputJwtToken = 'test';

        $userLogin = new UserLogin(
            'test@email.de',
            'test',
        );
        $userId = $this->loginHandler->handle($userLogin);

        self::assertSame('test', $userId);
        self::assertSame($user->getEmail(), $this->byEmailLoaderMock->inputEmail);
        self::assertSame($userLogin->password, $this->argon2idHasherMock->inputPlainPassword);
        self::assertSame($user->getPassword(), $this->argon2idHasherMock->inputHash);
        self::assertSame($user->getId(), $this->jwtUserTokenMock->inputUser->getId());
        self::assertSame($user->getFirstName(), $this->jwtUserTokenMock->inputUser->getFirstName());
        self::assertSame($user->getLastName(), $this->jwtUserTokenMock->inputUser->getLastName());
        self::assertSame($user->getEmail(), $this->jwtUserTokenMock->inputUser->getEmail());
        self::assertSame($user->getPassword(), $this->jwtUserTokenMock->inputUser->getPassword());
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testHandleWithUserLoginFailedException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->argon2idHasherMock->outputVerified = false;
        $this->jwtUserTokenMock->outputJwtToken = 'test';

        $userLogin = new UserLogin(
            'test@email.de',
            'test',
        );
        try {
            $this->loginHandler->handle($userLogin);
            $this->fail('UserLoginFailedException was expected to be thrown.');
        } catch (UserLoginFailedException $exception) {
            self::assertSame('Login user "test@email.de" was not successful.', $exception->getMessage());
        }

        self::assertSame($user->getEmail(), $this->byEmailLoaderMock->inputEmail);
        self::assertSame($userLogin->password, $this->argon2idHasherMock->inputPlainPassword);
        self::assertSame($user->getPassword(), $this->argon2idHasherMock->inputHash);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User login failed.',
                    'context' => [
                        'exceptionMessage' => 'Login user "test@email.de" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testHandleWithLoadEntityFailedException(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test@email.de',
            'test',
        );
        $this->byEmailLoaderMock->outputUser = $user;
        $this->byEmailLoaderMock->throwException = LoadEntityFailedException::loadFailed(User::class);
        $this->argon2idHasherMock->outputVerified = false;
        $this->jwtUserTokenMock->outputJwtToken = 'test';

        $userLogin = new UserLogin(
            'test@email.de',
            'test',
        );
        try {
            $this->loginHandler->handle($userLogin);
            $this->fail('UserLoginFailedException was expected to be thrown.');
        } catch (UserLoginFailedException $exception) {
            self::assertSame('Login user "test@email.de" was not successful.', $exception->getMessage());
        }

        self::assertSame($user->getEmail(), $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User login failed.',
                    'context' => [
                        'exceptionMessage' => 'Load entity "CuriosityConference\Adapters\Outbound\Persistence\Entity\User" was not successful.',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
