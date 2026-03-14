<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Device;

use CuriosityConference\Application\Port\Inbound\Device\DeleteHandler;
use CuriosityConference\Domain\Device\RegistrationToken;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class DeleteController
{
    public function __construct(
        private DeleteHandler $deleteHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $registrationToken = new RegistrationToken(
                $request->query->getString('registrationToken'),
            );
            $this->deleteHandler->handle($registrationToken);

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for DELETE to /api/v1/device is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (DeleteFailedException $exception) {
            $this->logger->notice('Device could not be deleted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
