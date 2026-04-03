<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Application\Port\Inbound\Quiz\ListHandler;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ListController
{
    public function __construct(
        private ListHandler $listHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $quizCollection = $this->listHandler->handle();

            return new JsonResponse(['data' => \array_map(static function (Quiz $quiz) {
                return [
                    'id' => $quiz->getId(),
                    'title' => $quiz->getTitle(),
                    'description' => $quiz->getDescription(),
                    'category' => $quiz->getCategory(),
                ];
            }, $quizCollection->data)], Response::HTTP_OK);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Quizes could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
