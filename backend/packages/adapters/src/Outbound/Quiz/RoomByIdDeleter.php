<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdDeleter as RoomByIdDeleterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class RoomByIdDeleter implements RoomByIdDeleterInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function delete(Id $id): void
    {
        try {
            $this->logger->info('Start deleting quiz room.', [
                'id' => $id->value,
            ]);
            $quizRoomEntity = $this->quizRoomRepository->findOneBy([
                'id' => $id->value,
            ]);
            if (!$quizRoomEntity instanceof QuizRoom) {
                return;
            }

            $this->quizRoomRepository->remove($quizRoomEntity);
            $this->logger->info('Deleting quiz room was successful.', [
                'id' => $id->value,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting quiz room was not successful.', [
                'id' => $id->value,
            ]);
            $this->logger->debug('Deleting quiz room was not successful.', [
                'id' => $id->value,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(QuizRoom::class);
        }
    }
}
