<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Loader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoadController
{
    public function __construct(
        private Loader $loader,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        try {
            $answerCollection = $this->loader->load();

            return new JsonResponse([
                'data' => \array_map(static function (QuestionnaireAnswer $questionnaireAnswer) {
                    return [
                        'question' => [
                            'id' => $questionnaireAnswer->getQuestionnaireQuestion()->getId(),
                            'title' => $questionnaireAnswer->getQuestionnaireQuestion()->getTitle(),
                        ],
                        'answer' => [
                            'id' => $questionnaireAnswer->getId(),
                            'title' => $questionnaireAnswer->getTitle(),
                        ],
                    ];
                }, $answerCollection->data),
            ], Response::HTTP_OK);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Questionnaire questions could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
