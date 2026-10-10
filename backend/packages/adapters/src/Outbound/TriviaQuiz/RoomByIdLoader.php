<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomByIdLoader as RoomByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class RoomByIdLoader implements RoomByIdLoaderInterface
{
    public function __construct(
        private TriviaQuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(string $id): TriviaQuizRoom
    {
        try {
            $this->logger->info('Start loading quiz room by id.', [
                'id' => $id,
            ]);
            $quizRoom = $this->quizRoomRepository->findOneBy(['id' => $id]);
            if (!$quizRoom instanceof TriviaQuizRoom) {
                throw LoadEntityFailedException::loadFailed(TriviaQuizRoom::class);
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

            throw LoadEntityFailedException::loadFailed(TriviaQuizRoom::class);
        }
    }
}
