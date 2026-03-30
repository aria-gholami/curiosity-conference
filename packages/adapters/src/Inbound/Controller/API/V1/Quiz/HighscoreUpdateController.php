<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\HighscoreUpdateHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\HighscoreUpdateRequestBody;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class HighscoreUpdateController
{
    public function __construct(
        private HighscoreUpdateHandler $highscoreUpdateHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $highscoreUpdateRequestBody = new HighscoreUpdateRequestBody(
                $data['quizRoomId'] ?? '',
                $data['userId'] ?? '',
                $data['answerId'] ?? '',
            );
            $highscoreUpdateResponse = $this->highscoreUpdateHandler->handleAndGet($highscoreUpdateRequestBody);

            return new JsonResponse(['highscore' => $highscoreUpdateResponse->highscore->getScore(), 'isCorrect' => $highscoreUpdateResponse->answer->isCorrect()], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for PUT to /api/v1/highscore is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException|UpdateFailedException $exception) {
            $this->logger->notice('Highscore could not be updated.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
