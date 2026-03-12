<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Security;

use CuriosityConference\Adapters\Inbound\Security\ValidateJwtSubscriber;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Security\Mocks\JwtValidatorMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\UseCase\Security\JwtValidator;
use CuriosityConference\Domain\Exception\InvalidJwtException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelInterface;

#[CoversClass(ValidateJwtSubscriber::class)]
final class ValidateJwtSubscriberTest extends TestCase
{
    private JwtValidatorMock $jwtValidatorMock;
    private LoggerMock $loggerMock;
    private ValidateJwtSubscriber $validateJwtSubscriber;

    protected function setUp(): void
    {
        $this->jwtValidatorMock = new JwtValidatorMock();
        $this->loggerMock = new LoggerMock();
        $this->validateJwtSubscriber = new ValidateJwtSubscriber($this->jwtValidatorMock, $this->loggerMock);
    }

    public function testValidateJwtIsValid(): void
    {
        $kernel = $this->createStub(KernelInterface::class);
        $request = new Request(
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer valid-token',
            ],
        );
        $requestEvent = new RequestEvent($kernel, $request, 1);
        $this->validateJwtSubscriber->validateJwt($requestEvent);

        self::assertSame('Bearer valid-token', $this->jwtValidatorMock->inputRequest->headers->get(JwtValidator::HEADER_NAME));
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testValidateJwtIsInvalid(): void
    {
        $this->jwtValidatorMock->throwException = new InvalidJwtException('test');
        $kernel = $this->createStub(KernelInterface::class);
        $request = new Request(
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
            ],
        );
        $requestEvent = new RequestEvent($kernel, $request, 1);
        $requestEvent = $this->validateJwtSubscriber->validateJwt($requestEvent);

        $response = $requestEvent->getResponse();
        self::assertSame(401, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            \json_encode(['message' => 'Unauthorized.'], \JSON_THROW_ON_ERROR),
            $response->getContent(),
        );
        self::assertSame('Bearer invalid-token', $this->jwtValidatorMock->inputRequest->headers->get(JwtValidator::HEADER_NAME));
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Invalid jwt provided.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
