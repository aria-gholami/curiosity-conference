<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\PersistHandler;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;
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
                throw new EmptyStringException('Request data for /api/v1/trivia/questions is invalid.');
            }

            $this->persistHandler->handle($data);

            return new JsonResponse([
                'message' => 'Success.',
            ], Response::HTTP_CREATED);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for POST to /api/v1/trivia/questions is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (CorrectAnswerNotExistingException $exception) {
            $this->logger->notice('Quiz could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Conflict.'], Response::HTTP_CONFLICT);
        } catch (PersistFailedException|DeleteFailedException $exception) {
            $this->logger->notice('Quiz could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
