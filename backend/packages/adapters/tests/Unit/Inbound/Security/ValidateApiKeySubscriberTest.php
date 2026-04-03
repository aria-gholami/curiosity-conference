<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Inbound\Security;

use CuriosityConference\Adapters\Inbound\Security\ValidateApiKeySubscriber;
use CuriosityConference\Adapters\Tests\Unit\Inbound\Security\Mocks\ApiKeyValidatorMock;
use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\UseCase\Security\ApiKeyValidator;
use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelInterface;

#[CoversClass(ValidateApiKeySubscriber::class)]
final class ValidateApiKeySubscriberTest extends TestCase
{
    private ApiKeyValidatorMock $apiKeyValidatorMock;
    private LoggerMock $loggerMock;
    private ValidateApiKeySubscriber $validateApiKeySubscriber;

    protected function setUp(): void
    {
        $this->apiKeyValidatorMock = new ApiKeyValidatorMock();
        $this->loggerMock = new LoggerMock();
        $this->validateApiKeySubscriber = new ValidateApiKeySubscriber($this->apiKeyValidatorMock, $this->loggerMock);
    }

    public function testValidateApiKeyIsValid(): void
    {
        $kernel = $this->createStub(KernelInterface::class);
        $request = new Request(
            server: [
                'HTTP_API_KEY' => 'valid-key',
            ],
        );
        $requestEvent = new RequestEvent($kernel, $request, 1);
        $this->validateApiKeySubscriber->validateApiKey($requestEvent);

        self::assertSame('valid-key', $this->apiKeyValidatorMock->inputRequest->headers->get(ApiKeyValidator::HEADER_NAME));
        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testValidateApiKeyIsInvalid(): void
    {
        $this->apiKeyValidatorMock->thrownException = new InvalidApiKeyException('test');
        $kernel = $this->createStub(KernelInterface::class);
        $request = new Request(
            server: [
                'HTTP_API_KEY' => 'invalid-key',
            ],
        );
        $requestEvent = new RequestEvent($kernel, $request, 1);
        $requestEvent = $this->validateApiKeySubscriber->validateApiKey($requestEvent);

        self::assertSame(401, $requestEvent->getResponse()->getStatusCode());
        self::assertSame('{"message":"Unauthorized."}', $requestEvent->getResponse()->getContent());
        self::assertSame('invalid-key', $this->apiKeyValidatorMock->inputRequest->headers->get(ApiKeyValidator::HEADER_NAME));
        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Invalid api key provided.',
                    'context' => [],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
