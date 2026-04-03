<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Tests\Unit\UseCase\Security;

use CuriosityConference\Adapters\Tests\Unit\Mocks\LoggerMock;
use CuriosityConference\Application\UseCase\Security\ApiKeyValidator;
use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(ApiKeyValidator::class)]
final class ApiKeyValidatorTest extends TestCase
{
    private LoggerMock $loggerMock;
    private ApiKeyValidator $apiKeyValidator;

    protected function setUp(): void
    {
        $this->loggerMock = new LoggerMock();
        $this->apiKeyValidator = new ApiKeyValidator('api-key', $this->loggerMock);
    }

    public function testValidateRequestIsValid(): void
    {
        $request = new Request();
        $request->headers->set(ApiKeyValidator::HEADER_NAME, 'api-key');
        $this->apiKeyValidator->validateRequest($request);

        self::assertEquals([], $this->loggerMock->logs);
    }

    public function testValidateRequestWithInvalidApiKey(): void
    {
        $request = new Request();
        $request->headers->set(ApiKeyValidator::HEADER_NAME, 'api-key-invalid');
        try {
            $this->apiKeyValidator->validateRequest($request);
            $this->fail('InvalidApiKeyException was expected to be thrown.');
        } catch (InvalidApiKeyException $exception) {
            self::assertSame('Authentication failed with error: "Api key is invalid."', $exception->getMessage());
        }

        self::assertEquals([
            'notice' => [
                [
                    'message' => 'Invalid api key provided.',
                    'context' => [
                        'required-header' => 'api-key',
                    ],
                ],
            ],
            'debug' => [
                [
                    'message' => 'Invalid api key provided.',
                    'context' => [
                        'required-header' => 'api-key',
                        'key' => 'api-key-invalid',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }

    public function testValidateRequestWithMissingHeader(): void
    {
        $request = new Request();
        try {
            $this->apiKeyValidator->validateRequest($request);
            $this->fail('InvalidApiKeyException was expected to be thrown.');
        } catch (InvalidApiKeyException $exception) {
            self::assertSame('Authentication failed with error: "Header "api-key" is not set."', $exception->getMessage());
        }

        self::assertEquals([
            'notice' => [
                [
                    'message' => 'No api key header found in request.',
                    'context' => [
                        'required-header' => 'api-key',
                    ],
                ],
            ],
        ], $this->loggerMock->logs);
    }
}
