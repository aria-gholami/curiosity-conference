<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\Question;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Application\Port\Inbound\Question\ByRoomLoadHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ByRoomLoadController
{
    public function __construct(
        private ByRoomLoadHandler $byRoomLoadHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $id = new Id(
                $request->attributes->getString('quizRoomId'),
            );
            $question = $this->byRoomLoadHandler->handleAndGet($id);

            return new JsonResponse(
                [
                    'question' => $question->getTitle(),
                    'answers' => \array_map(static function (Answer $answer) {
                        return [
                            'id' => $answer->getId(),
                            'title' => $answer->getTitle(),
                            'isCorrect' => $answer->isCorrect(),
                        ];
                    }, $question->getAnswerCollection()->getValues())],
                Response::HTTP_OK,
            );
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/quiz/room/{quizRoomId}/question is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (LoadEntityFailedException $exception) {
            $this->logger->notice('Current question by quiz room could not be loaded.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
