<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\RoomLoader as RoomLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\QuizRoomCollection;
use Psr\Log\LoggerInterface;

final readonly class RoomLoader implements RoomLoaderInterface
{
    public function __construct(
        private QuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(): QuizRoomCollection
    {
        try {
            $this->logger->info('Start loading quiz rooms.');
            $quizEntityGroup = $this->quizRoomRepository->findAll();
            $quizRoomCollection = new QuizRoomCollection($quizEntityGroup);
            if ($quizRoomCollection->isEmpty()) {
                throw LoadEntityFailedException::loadFailed(QuizRoom::class);
            }

            $this->logger->info('Loading quiz rooms was successful.');

            return $quizRoomCollection;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz rooms was not successful.');
            $this->logger->debug('Loading quiz rooms was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(QuizRoom::class);
        }
    }
}
