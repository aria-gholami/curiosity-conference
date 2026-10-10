<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Application\Port\Inbound\TriviaQuiz\ListHighscoreByAllRoomsHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomHighscore;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ListHighscoreByAllRoomsController
{
    public function __construct(
        private ListHighscoreByAllRoomsHandler $listHighscoreByAllRoomHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $quizCollection = $this->listHighscoreByAllRoomHandler->handleAndGet();

            return new JsonResponse(['data' => \array_map(static function (QuizRoomHighscore $quizRoomHighscore) {
                return [
                    'quizRoomTitle' => $quizRoomHighscore->title,
                    'quizRoomScore' => $quizRoomHighscore->score,
                ];
            }, $quizCollection->data)], Response::HTTP_OK);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Highscores by all rooms could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
