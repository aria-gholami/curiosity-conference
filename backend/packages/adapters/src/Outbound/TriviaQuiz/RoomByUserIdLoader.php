<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByUserIdLoader as RoomByUserIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomByUserIdLoader implements RoomByUserIdLoaderInterface
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
    public function load(string $userId): ?TriviaQuizRoom
    {
        try {
            $this->logger->info('Start loading quiz room by participant id.');
            $quizRoom = $this->quizRoomRepository->findOneByParticipantId($userId);
            $this->logger->info('Loading quiz room by participant id was successful.');

            return $quizRoom;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz room by participant id was not successful.');
            $this->logger->debug('Loading quiz room by participant id was not successful.', [
                'userId' => $userId,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuizRoom::class);
        }
    }
}
