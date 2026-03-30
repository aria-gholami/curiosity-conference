<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Application\Port\Inbound\User\DetailsGetHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\User\Model\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class DetailsGetController
{
    public function __construct(
        private DetailsGetHandler $detailsGetHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $id = new Id(
                $request->query->getString('id'),
            );
            $user = $this->detailsGetHandler->handleAndGet($id);

            return new JsonResponse([
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'highscores' => \array_map(static function (Highscore $highscore) {
                    return [
                        'id' => $highscore->getId(),
                        'quizRoomId' => $highscore->getQuizRoom()->getId(),
                        'quizRoomStatus' => $highscore->getQuizRoom()->getStatus(),
                        'quizId' => $highscore->getQuizRoom()->getQuiz()->getId(),
                        'quizTitle' => $highscore->getQuizRoom()->getQuiz()->getTitle(),
                        'quizDescription' => $highscore->getQuizRoom()->getQuiz()->getDescription(),
                        'quizCategory' => $highscore->getQuizRoom()->getQuiz()->getCategory(),
                        'score' => $highscore->getScore(),
                    ];
                }, $user->getHighscores()->toArray()),
            ], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/user is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('User details could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
