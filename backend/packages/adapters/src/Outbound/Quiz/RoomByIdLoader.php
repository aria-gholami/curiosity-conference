<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomByIdLoader as RoomByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomByIdLoader implements RoomByIdLoaderInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(string $id): QuizRoom
    {
        try {
            $this->logger->info('Start loading quiz room by id.', [
                'id' => $id,
            ]);
            $quizRoom = $this->quizRoomRepository->findOneBy(['id' => $id]);
            if (!$quizRoom instanceof QuizRoom) {
                throw LoadEntityFailedException::loadFailed(QuizRoom::class);
            }

            $this->logger->info('Loading quiz room by id was successful.', [
                'id' => $id,
            ]);

            return $quizRoom;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz room by id was not successful.');
            $this->logger->debug('Loading quiz room by id was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(QuizRoom::class);
        }
    }
}
