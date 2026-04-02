<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Application\Port\Inbound\Quiz\ContinueOrFinishHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\ContinueOrFinishRequestBody;
use CuriosityConference\Domain\Quiz\Exception\CurrentQuestionNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ContinueOrFinishController
{
    public function __construct(
        private ContinueOrFinishHandler $continueOrFinishHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $highscoreUpdateRequestBody = new ContinueOrFinishRequestBody(
                $data['quizRoomId'] ?? '',
                $data['userId'] ?? '',
                $data['answerId'] ?? '',
            );
            $highscoreUpdateResponse = $this->continueOrFinishHandler->handleAndGet($highscoreUpdateRequestBody);

            return new JsonResponse(['highscore' => $highscoreUpdateResponse->highscore->getScore(),
                'isCorrect' => $highscoreUpdateResponse->answer->isCorrect()], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data to /api/v1/quiz/room/continue is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException|UpdateFailedException|PersistFailedException|CurrentQuestionNotFoundException $exception) {
            $this->logger->notice('Question could not be continues or quiz could not be finished.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
