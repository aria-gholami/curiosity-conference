<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ActiveRoomLoadHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ActiveRoomLoadController
{
    public function __construct(
        private ActiveRoomLoadHandler $activeRoomLoadHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $userId = new Id($request->attributes->getString('userId'));
            $quizRoom = $this->activeRoomLoadHandler->handleAndGet($userId);
            if (!$quizRoom instanceof TriviaQuizRoom) {
                return new JsonResponse([], Response::HTTP_NO_CONTENT);
            }

            return new JsonResponse([
                'id' => $quizRoom->getId(),
                'title' => $quizRoom->getTitle(),
                'host' => [
                    'id' => $quizRoom->getHost()->getId(),
                    'userName' => $quizRoom->getHost()->getUserName(),
                ],
                'status' => $quizRoom->getStatus(),
                'participants' => \array_map(static function (User $participant) {
                    return [
                        'userId' => $participant->getId(),
                        'userName' => $participant->getUserName(),
                    ];
                }, $quizRoom->getTriviaParticipants()->toArray()),
            ], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/trivia/quiz/room/user/{userId} is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Active room could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
