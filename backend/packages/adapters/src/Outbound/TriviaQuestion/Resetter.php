<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Resetter as ResetterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class Resetter implements ResetterInterface
{
    public function __construct(
        private TriviaQuestionRepository $questionRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws DeleteFailedException
     */
    #[\Override]
    public function reset(): void
    {
        try {
            $this->logger->info('Start deleting all questions.');
            $questionCollection = $this->questionRepository->findAll();
            foreach ($questionCollection as $questionEntity) {
                $this->questionRepository->remove($questionEntity);
            }

            $this->logger->info('Deleting all questions was successful.');
        } catch (\Throwable $exception) {
            $this->logger->notice('Deleting all questions was not successful.');
            $this->logger->debug('Deleting all questions was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(TriviaQuestion::class);
        }
    }
}
