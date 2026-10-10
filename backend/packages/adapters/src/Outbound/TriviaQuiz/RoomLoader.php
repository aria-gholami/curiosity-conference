<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRoomRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\RoomLoader as RoomLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomCollection;
use Psr\Log\LoggerInterface;

final readonly class RoomLoader implements RoomLoaderInterface
{
    public function __construct(
        private TriviaQuizRoomRepository $quizRoomRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(): QuizRoomCollection
    {
        try {
            $this->logger->info('Start loading quiz rooms.');
            $quizEntityGroup = $this->quizRoomRepository->findAll();
            $this->logger->info('Loading quiz rooms was successful.');

            return new QuizRoomCollection($quizEntityGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz rooms was not successful.');
            $this->logger->debug('Loading quiz rooms was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuizRoom::class);
        }
    }
}
