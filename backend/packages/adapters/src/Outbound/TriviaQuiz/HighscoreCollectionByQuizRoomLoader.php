<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaHighscoreRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\HighscoreCollectionByQuizRoomLoader as HighscoreCollectionByQuizRoomLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuiz\HighscoreCollection;
use Psr\Log\LoggerInterface;

final readonly class HighscoreCollectionByQuizRoomLoader implements HighscoreCollectionByQuizRoomLoaderInterface
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
    public function load(TriviaQuizRoom $quizRoom): HighscoreCollection
    {
        try {
            $this->logger->info('Start loading highscores.', [
                'quizRoomId' => $quizRoom->getId(),
            ]);
            $highscoreGroup = $this->highscoreRepository->findBy(['triviaQuizRoom' => $quizRoom]);
            if (0 === \count($highscoreGroup)) {
                throw LoadEntityFailedException::loadFailed(TriviaHighscore::class);
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

            throw LoadEntityFailedException::loadFailed(TriviaHighscore::class);
        }
    }
}
