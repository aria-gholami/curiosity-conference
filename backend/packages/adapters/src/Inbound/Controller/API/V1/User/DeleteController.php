<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\DeleteHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class DeleteController
{
    public function __construct(
        private DeleteHandler $deleteHandler,
        private HubInterface $hub,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $userId = new Id(
                $request->query->getString('userId'),
            );
            $this->deleteHandler->handle($userId);

            $update = new Update(
                'quiz/room/list',
                \json_encode(\array_merge(['action' => 'remove']), \JSON_THROW_ON_ERROR),
            );
            $this->hub->publish($update);

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for DELETE to /api/v1/user is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (DeleteFailedException $exception) {
            $this->logger->notice('User could not be deleted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
