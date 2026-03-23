<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\CreateRoomHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\QuizRoom;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateRoomController
{
    public function __construct(
        private CreateRoomHandler $createRoomHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $quizRoom = new QuizRoom(
                $data['quizId'] ?? '',
                $data['hostId'] ?? '',
            );
            $id = $this->createRoomHandler->handleAndGet($quizRoom);

            return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for /api/v1/quiz/room is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PersistFailedException|LoadEntityFailedException $exception) {
            $this->logger->notice('New quiz room could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
