<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Application\Port\Inbound\Quiz\ListHighscoreByRoomHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ListHighscoreByRoomController
{
    public function __construct(
        private ListHighscoreByRoomHandler $listHighscoreByRoomHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $quizRoomId = new Id($request->attributes->getString('quizRoomId'));
            $quizCollection = $this->listHighscoreByRoomHandler->handleAndGet($quizRoomId);

            return new JsonResponse(['data' => \array_map(static function (Highscore $highscore) {
                return [
                    'firstName' => $highscore->getUser()->getFirstName(),
                    'lastName' => $highscore->getUser()->getLastName(),
                    'score' => $highscore->getScore(),
                ];
            }, $quizCollection->data)], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/quiz/room/{quizRoomId}/highscores is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Highscores could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
