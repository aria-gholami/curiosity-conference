<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Application\Port\Inbound\TriviaQuestion\ByRoomLoadHandler as ByRoomLoadHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByRoomLoadHandler implements ByRoomLoadHandlerInterface
{
    public function __construct(
        private RoomByIdLoader $roomByIdLoader,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handleAndGet(Id $id): TriviaQuestion
    {
        $currentQuestion = $this->roomByIdLoader->load($id->value)->getCurrentTriviaQuestion();
        if (!$currentQuestion instanceof TriviaQuestion) {
            $this->logger->notice('Quiz room has no current question.', [
                'quizRoomId' => $id->value,
            ]);
            throw LoadEntityFailedException::loadFailed(TriviaQuestion::class);
        }

        return $currentQuestion;
    }
}
