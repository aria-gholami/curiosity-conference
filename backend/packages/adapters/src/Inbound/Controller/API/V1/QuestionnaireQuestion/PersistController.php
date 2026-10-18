<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Application\Port\Inbound\QuestionnaireQuestion\PersistHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\EmptyAnswerArrayException;
use CuriosityConference\Domain\QuestionnaireQuestion\Exception\InvalidQuestionDataByCMSException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PersistController
{
    public function __construct(
        private PersistHandler $persistHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            if (!\is_array($data)) {
                throw InvalidQuestionDataByCMSException::isInvalid('questionDataByCMS');
            }

            $this->persistHandler->handle($data);

            return new JsonResponse([
                'message' => 'Success.',
            ], Response::HTTP_CREATED);
        } catch (InvalidQuestionDataByCMSException|EmptyStringException|EmptyAnswerArrayException $exception) {
            $this->logger->notice('Request data for POST to /api/v1/questionnaire/questions is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PersistFailedException|DeleteFailedException $exception) {
            $this->logger->notice('Questionnaire Question data could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
