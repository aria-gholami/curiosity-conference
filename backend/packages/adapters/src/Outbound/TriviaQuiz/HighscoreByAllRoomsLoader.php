<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreByAllRoomsLoader as HighscoreByAllRoomsLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\AllRoomsHighscoreCollection;
use Psr\Log\LoggerInterface;

final readonly class HighscoreByAllRoomsLoader implements HighscoreByAllRoomsLoaderInterface
{
    public function __construct(
        private TriviaHighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): AllRoomsHighscoreCollection
    {
        try {
            $this->logger->info('Start loading highscore from all quiz rooms.');
            $allRoomsHighscoreGroup = $this->highscoreRepository->loadFromAllRooms();
            $this->logger->info('Loading highscore from all quiz rooms was successful.');

            return new AllRoomsHighscoreCollection($allRoomsHighscoreGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading highscore from all quiz rooms was not successful.');
            $this->logger->debug('Loading highscore from all quiz rooms was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaHighscore::class);
        }
    }
}
