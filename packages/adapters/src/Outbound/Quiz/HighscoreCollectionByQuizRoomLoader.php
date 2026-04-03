<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Highscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\HighscoreRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\HighscoreCollectionByQuizRoomLoader as HighscoreCollectionByQuizRoomLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\HighscoreCollection;
use Psr\Log\LoggerInterface;

final readonly class HighscoreCollectionByQuizRoomLoader implements HighscoreCollectionByQuizRoomLoaderInterface
{
    public function __construct(
        private HighscoreRepository $highscoreRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(QuizRoom $quizRoom): HighscoreCollection
    {
        try {
            $this->logger->info('Start loading highscores.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $highscoreGroup = $this->highscoreRepository->findBy(['quizRoom' => $quizRoom]);
            if (0 === \count($highscoreGroup)) {
                throw LoadEntityFailedException::loadFailed(Highscore::class);
            }
            $this->logger->info('Loading highscores was successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);

            return new HighscoreCollection($highscoreGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading highscores was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $this->logger->debug('Loading highscores was not successful.', [
                'quizRoomId' => $quizRoom->getId(),
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(Highscore::class);
        }
    }
}
