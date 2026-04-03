<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Security;

use CuriosityConference\Application\Port\Inbound\Security\ApiKeyValidator;
use CuriosityConference\Domain\Exception\InvalidApiKeyException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class ValidateApiKeySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ApiKeyValidator $apiKeyValidator,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @codeCoverageIgnore
     *
     * @infection-ignore-all
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['validateApiKey', 10],
        ];
    }

    public function validateApiKey(RequestEvent $requestEvent): RequestEvent
    {
        try {
            $this->apiKeyValidator->validateRequest($requestEvent->getRequest());
        } catch (InvalidApiKeyException) {
            $this->logger->notice('Invalid api key provided.');
            $requestEvent->setResponse(new JsonResponse([
                'message' => 'Unauthorized.',
            ], 401));
        }

        return $requestEvent;
    }
}
