<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\JoinRoomHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Exception\QuizRoomIsAlreadyStartedException;
use CuriosityConference\Domain\Quiz\JoinQuizRoom;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class JoinRoomController
{
    public function __construct(
        private JoinRoomHandler $joinRoomHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $joinQuizRoom = new JoinQuizRoom(
                $data['quizRoomId'] ?? '',
                $data['userId'] ?? '',
            );
            $id = $this->joinRoomHandler->handleAndGet($joinQuizRoom);

            return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for /api/v1/quiz/room/join is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (UpdateFailedException|LoadEntityFailedException|QuizRoomIsAlreadyStartedException $exception) {
            $this->logger->notice('Quiz room could not be joined.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
