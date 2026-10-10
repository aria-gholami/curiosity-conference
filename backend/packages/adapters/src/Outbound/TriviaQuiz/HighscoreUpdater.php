<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreUpdater as HighscoreUpdaterInterface;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use Psr\Log\LoggerInterface;

final readonly class HighscoreUpdater implements HighscoreUpdaterInterface
{
    public function __construct(
        private TriviaHighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UpdateFailedException
     */
    #[\Override]
    public function update(TriviaHighscore $highscore): void
    {
        try {
            $this->logger->info('Start updating highscore.', [
                'highscoreId' => $highscore->getId(),
            ]);
            $this->highscoreRepository->flush();
            $this->logger->info('Updating highscore was successful.', [
                'highscoreId' => $highscore->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->notice('Updating highscore was not successful.', [
                'highscoreId' => $highscore->getId(),
            ]);
            $this->logger->debug('Updating highscore was not successful.', [
                'highscoreId' => $highscore->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw UpdateFailedException::updateFailed(TriviaHighscore::class);
        }
    }
}
