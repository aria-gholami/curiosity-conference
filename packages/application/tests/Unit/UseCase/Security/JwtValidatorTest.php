<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Security;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\Tests\Unit\Mocks\ByEmailLoaderMock;
use CuriosityConference\Application\Tests\Unit\Mocks\JwtUserTokenMock;
use CuriosityConference\Application\UseCase\Security\JwtValidator;
use CuriosityConference\Domain\Exception\InvalidJwtException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(JwtValidator::class)]
final class JwtValidatorTest extends TestCase
{
    private JwtUserTokenMock $jwtUserTokenMock;
    private ByEmailLoaderMock $byEmailLoaderMock;
    private LoggerMock $loggerMock;
    private JwtValidator $jwtValidator;

    protected function setUp(): void
    {
        $this->jwtUserTokenMock = new JwtUserTokenMock();
        $this->byEmailLoaderMock = new ByEmailLoaderMock();
        $this->loggerMock = new LoggerMock();
        $this->jwtValidator = new JwtValidator(
            $this->jwtUserTokenMock,
            $this->byEmailLoaderMock,
            $this->loggerMock,
        );
    }

    public function testValidateRequestIsValid(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test', 'POST');
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        $this->jwtValidator->validateRequest($request);

        self::assertEquals('jwt-token', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([], $this->loggerMock->logs);
    }

    #[DataProvider('provideUserLoginUrlData')]
    public function testValidateRequestWithWrongUrls(string $path, string $method): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create($path, $method);
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        $this->jwtValidator->validateRequest($request);

        self::assertSame('', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'info' => [
                [
                    'message' => 'Skip jwt validation.',
                    'context' => [
                        'isLogin' => '/api/v1/user/login' === $path && 'POST' === $method,
                        'isUserCreate' => '/api/v1/user' === $path && 'POST' === $method,
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public static function provideUserLoginUrlData(): iterable
    {
        yield 'login endpoint' => ['/api/v1/user/login', 'POST'];
        yield 'user create endpoint' => ['/api/v1/user', 'POST'];
        yield 'user password reset' => ['/api/v1/password/reset', 'POST'];
        yield 'user password update' => ['/api/v1/user/password', 'PUT'];
    }

    public function testValidateRequestWithMissingHeader(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test', 'POST');
        try {
            $this->jwtValidator->validateRequest($request);
            $this->fail('InvalidJwtException was expected to be thrown.');
        } catch (InvalidJwtException $exception) {
            self::assertSame('Authentication failed with error: "Jwt is invalid."', $exception->getMessage());
        }

        self::assertEquals([
            'notice' => [
                [
                    'message' => 'No authorization header found in request.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                        'exceptionMessage' => 'Authentication failed with error: "Header "Authorization" is not set."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testValidateRequestWithMissingBearer(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test', 'POST');
        $request->headers->set(JwtValidator::HEADER_NAME, '');
        try {
            $this->jwtValidator->validateRequest($request);
            $this->fail('InvalidJwtException was expected to be thrown.');
        } catch (InvalidJwtException $exception) {
            self::assertSame('Authentication failed with error: "Jwt is invalid."', $exception->getMessage());
        }

        self::assertEquals([
            'notice' => [
                [
                    'message' => 'No bearer token header found in request.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                        'exceptionMessage' => 'Authentication failed with error: "Jwt is invalid."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testValidateRequestWithMissingBearerToken(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test', 'POST');
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer ');
        try {
            $this->jwtValidator->validateRequest($request);
            $this->fail('InvalidJwtException was expected to be thrown.');
        } catch (InvalidJwtException $exception) {
            self::assertSame('Authentication failed with error: "Jwt is invalid."', $exception->getMessage());
        }

        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Bearer token is undefined.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                        'exceptionMessage' => 'Authentication failed with error: "Jwt is invalid."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testValidateRequestWithRoomCreateAndNoAdminRole(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/quiz/room', 'POST');
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        try {
            $this->jwtValidator->validateRequest($request);
            $this->fail('InvalidJwtException was expected to be thrown.');
        } catch (InvalidJwtException $exception) {
            self::assertSame('Authentication failed with error: "Jwt is invalid."', $exception->getMessage());
        }

        self::assertEquals('jwt-token', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User is not admin.',
                    'context' => [],
                ],
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                        'exceptionMessage' => 'Authentication failed with error: "Role is invalid."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    #[DataProvider('provideAdminUrlData')]
    public function testValidateRequestWithRoomStartAndNoAdminRole(string $path, string $method): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create($path, $method);
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        try {
            $this->jwtValidator->validateRequest($request);
            $this->fail('InvalidJwtException was expected to be thrown.');
        } catch (InvalidJwtException $exception) {
            self::assertSame('Authentication failed with error: "Jwt is invalid."', $exception->getMessage());
        }

        self::assertEquals('jwt-token', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'User is not admin.',
                    'context' => [],
                ],
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Bearer token is invalid.',
                    'context' => [
                        'required-header' => 'Authorization',
                        'exceptionMessage' => 'Authentication failed with error: "Role is invalid."',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public static function provideAdminUrlData(): iterable
    {
        yield 'room create endpoint' => ['/api/v1/quiz/room', 'POST'];
        yield 'room start endpoint' => ['/api/v1/quiz/room/start', 'POST'];
        yield 'room continue endpoint' => ['/api/v1/quiz/room/continue', 'POST'];
        yield 'participant delete endpoint' => ['/api/v1/quiz/room/1234/participant/1234', 'DELETE'];
    }

    public function testValidatePOSTRequestWithRoomStartAndNoAdminRole(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test', 'POST');
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        $this->jwtValidator->validateRequest($request);

        self::assertEquals('jwt-token', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testValidateDELETERequestWithRoomParticipantAndNoAdminRole(): void
    {
        $user = new User(
            'test',
            'test',
            'test',
            'test',
            'test',
            false,
        );
        $this->jwtUserTokenMock->outputUser = $user;
        $this->byEmailLoaderMock->outputUser = $user;

        $request = Request::create('/api/v1/test/participant/1234', 'DELETE');
        $request->headers->set(JwtValidator::HEADER_NAME, 'Bearer jwt-token');
        $this->jwtValidator->validateRequest($request);

        self::assertEquals('jwt-token', $this->jwtUserTokenMock->inputJwtToken);
        self::assertSame('test', $this->byEmailLoaderMock->inputEmail);
        self::assertEquals([], $this->loggerMock->logs);
    }
}
