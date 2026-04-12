<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Inbound\Quiz\CreateRoomHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Quiz\QuizRoom;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class CreateRoomController
{
    public function __construct(
        private CreateRoomHandler $createRoomHandler,
        private HubInterface $hub,
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
            $quizRoom = $this->createRoomHandler->handleAndGet($quizRoom);

            $response = [
                'id' => $quizRoom->getId(),
                'quiz' => [
                    'title' => $quizRoom->getQuiz()->getTitle(),
                ],
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
                }, $quizRoom->getParticipants()->toArray()),
            ];

            $update = new Update(
                'quiz/room/list',
                \json_encode($response, \JSON_THROW_ON_ERROR),
            );
            $this->hub->publish($update);

            return new JsonResponse($response, Response::HTTP_CREATED);
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
