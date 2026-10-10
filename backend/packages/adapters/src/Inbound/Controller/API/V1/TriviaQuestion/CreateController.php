<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Application\Port\Inbound\TriviaQuestion\CreateHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidNumberException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCreateDetails;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateController
{
    public function __construct(
        private CreateHandler $createHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $questionCreateDetails = new QuestionCreateDetails(
                $data['question'] ?? '',
                $data['quizId'] ?? '',
                $data['answer1'] ?? '',
                $data['answer2'] ?? '',
                $data['answer3'] ?? '',
                $data['answer4'] ?? '',
                $data['correctAnswerIndex'] ?? -1,
            );
            $id = $this->createHandler->handle($questionCreateDetails);

            return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
        } catch (EmptyStringException|InvalidNumberException $exception) {
            $this->logger->notice('Request data for POST to /api/v1/trivia/question is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PersistFailedException $exception) {
            $this->logger->notice('Question could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
