<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Inbound\Quiz\ListRoomHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ListRoomController
{
    public function __construct(
        private ListRoomHandler $listRoomHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $quizRoomCollection = $this->listRoomHandler->handleAndGet();

            return new JsonResponse(['data' => \array_map(static function (QuizRoom $quizRoom) {
                return [
                    'id' => $quizRoom->getId(),
                    'quizTitle' => $quizRoom->getQuiz()->getTitle(),
                    'quizDescription' => $quizRoom->getQuiz()->getDescription(),
                    'quizCategory' => $quizRoom->getQuiz()->getCategory(),
                    'hostUserName' => $quizRoom->getHost()->getUserName(),
                    'participants' => \array_map(static function (User $participant) {
                        return [
                            'userName' => $participant->getUserName(),
                        ];
                    }, $quizRoom->getParticipants()->toArray()),
                ];
            }, $quizRoomCollection->data)], Response::HTTP_OK);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Quiz rooms could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
