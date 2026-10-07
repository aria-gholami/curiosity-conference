<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByUserIdLoader as RoomByUserIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomByUserIdLoader implements RoomByUserIdLoaderInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $userId): ?QuizRoom
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

            throw LoadEntityFailedException::loadFailed(QuizRoom::class);
        }
    }
}
