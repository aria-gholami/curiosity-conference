<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\DetailsUpdateHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserDetails;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class DetailsUpdateController
{
    public function __construct(
        private DetailsUpdateHandler $editHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $userDetails = new UserDetails(
                $data['id'] ?? '',
                $data['firstName'] ?? '',
                $data['lastName'] ?? '',
            );
            $this->editHandler->handle($userDetails);

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for PUT to /api/v1/user is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (UpdateFailedException $exception) {
            $this->logger->notice('User details could not be updated.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
