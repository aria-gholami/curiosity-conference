<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuizRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByNameLoader as ByNameLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByNameLoader implements ByNameLoaderInterface
{
    public function __construct(
        private TriviaQuizRepository $quizRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(string $title): ?TriviaQuiz
    {
        try {
            $this->logger->info('Start loading quiz by name.', [
                'title' => $title,
            ]);
            $quiz = $this->quizRepository->findOneBy(['title' => $title]);

            $this->logger->info('Loading quiz by name was successful.', [
                'title' => $title,
            ]);

            return $quiz;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz by name was not successful.');
            $this->logger->debug('Loading quiz by name was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuiz::class);
        }
    }
}
