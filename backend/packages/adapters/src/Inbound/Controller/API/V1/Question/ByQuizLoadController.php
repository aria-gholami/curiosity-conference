<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
use CuriosityConference\Application\Port\Inbound\Question\ByQuizLoadHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ByQuizLoadController
{
    public function __construct(
        private ByQuizLoadHandler $listHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $id = new Id(
                $request->query->getString('quizId'),
            );
            $questionCollection = $this->listHandler->handle($id);

            return new JsonResponse(['data' => \array_map(static function (Question $question) {
                return [
                    'id' => $question->getId(),
                    'title' => $question->getTitle(),
                ];
            }, $questionCollection->data)], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/question is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Questions could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
