<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Security;

use CuriosityConference\Application\Port\Inbound\Security\ApiKeyValidator as ApiKeyValidatorInterface;
use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class ApiKeyValidator implements ApiKeyValidatorInterface
{
    public const string HEADER_NAME = 'api-key';

    public function __construct(
        private string $apiKey,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidApiKeyException
     */
    #[\Override]
    public function validateRequest(Request $request): void
    {
        if (false === $request->headers->has(self::HEADER_NAME)) {
            $this->logger->notice('No api key header found in request.', ['required-header' => self::HEADER_NAME]);
            throw InvalidApiKeyException::headerIsMissing();
        }

        if (!$this->requestContainsValidApiKey($request)) {
            $this->logger->notice('Invalid api key provided.', ['required-header' => self::HEADER_NAME]);
            $this->logger->debug('Invalid api key provided.', ['required-header' => self::HEADER_NAME, 'key' => $request->headers->get(self::HEADER_NAME)]);

            throw InvalidApiKeyException::invalidApiKey();
        }
    }

    private function requestContainsValidApiKey(Request $request): bool
    {
        return $request->headers->get(self::HEADER_NAME) === $this->apiKey;
    }
}
