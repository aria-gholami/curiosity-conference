<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Device;

use CuriosityConference\Application\Port\Inbound\Device\RegistrationHandler;
use CuriosityConference\Domain\Device\DeviceRegistration;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegistrationController
{
    public function __construct(
        private RegistrationHandler $deviceRegistrationHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $deviceRegistration = new DeviceRegistration(
                $data['registrationToken'] ?? '',
                $data['userId'] ?? '',
            );
            $this->deviceRegistrationHandler->handle($deviceRegistration);

            return new JsonResponse(['message' => 'Device is registered.'], Response::HTTP_CREATED);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for /api/v1/device is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PersistFailedException|LoadEntityFailedException $exception) {
            $this->logger->notice('Device could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
