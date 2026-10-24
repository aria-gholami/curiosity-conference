<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\TriviaQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\TriviaQuestionRepository;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Loader as LoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\TriviaQuestion\QuestionCollection;
use Psr\Log\LoggerInterface;

final readonly class Loader implements LoaderInterface
{
    public function __construct(
        private TriviaQuestionRepository $questionRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): QuestionCollection
    {
        try {
            $this->logger->info('Start loading all questions.');
            $questionGroup = $this->questionRepository->findAll();
            $this->logger->info('Loading all questions was successful.');

            return new QuestionCollection($questionGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading all questions was not successful.');
            $this->logger->debug('Loading all questions was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(TriviaQuestion::class);
        }
    }
}
