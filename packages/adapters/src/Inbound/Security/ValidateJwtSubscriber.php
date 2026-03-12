<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Security;

use CuriosityConference\Application\Port\Inbound\Security\JwtValidator;
use CuriosityConference\Domain\Exception\InvalidJwtException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class ValidateJwtSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JwtValidator $jwtValidator,
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
            'kernel.request' => ['validateJwt', 20],
        ];
    }

    public function validateJwt(RequestEvent $requestEvent): RequestEvent
    {
        try {
            $this->jwtValidator->validateRequest($requestEvent->getRequest());
        } catch (InvalidJwtException) {
            $this->logger->notice('Invalid jwt provided.');
            $requestEvent->setResponse(new JsonResponse([
                'message' => 'Unauthorized.',
            ], 401));
        }

        return $requestEvent;
    }
}
