<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomResultsByUserIdLoader as RoomResultsByUserIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomResultsByUserIdLoader implements RoomResultsByUserIdLoaderInterface
{
    public function __construct(
        private TriviaQuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $userId): array
    {
        try {
            $this->logger->info('Start loading quiz room results by participant id.');
            $quizRoomResults = $this->quizRoomRepository->findByParticipantId($userId);
            $this->logger->info('Loading quiz room results by participant id was successful.');

            return $quizRoomResults;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz room results by participant id was not successful.');
            $this->logger->debug('Loading quiz room results by participant id was not successful.', [
                'userId' => $userId,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuizRoom::class);
        }
    }
}
