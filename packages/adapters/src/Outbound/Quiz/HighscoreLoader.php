<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreLoader as HighscoreLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class HighscoreLoader implements HighscoreLoaderInterface
{
    public function __construct(
        private HighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(string $userId, string $quizRoomId): Highscore
    {
        try {
            $this->logger->info('Start loading highscore.', [
                'quizRoomId' => $quizRoomId,
            ]);
            $highscore = $this->highscoreRepository->findOneBy(['userId' => $userId, 'quizRoomId' => $quizRoomId]);
            if (!$highscore instanceof Highscore) {
                throw LoadEntityFailedException::loadFailed(Highscore::class);
            }
            $this->logger->info('Loading highscore was successful.', [
                'quizRoomId' => $quizRoomId,
            ]);

            return $highscore;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading highscore was not successful.', [
                'quizRoomId' => $quizRoomId,
            ]);
            $this->logger->debug('Loading highscore was not successful.', [
                'quizRoomId' => $quizRoomId,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(Highscore::class);
        }
    }
}
