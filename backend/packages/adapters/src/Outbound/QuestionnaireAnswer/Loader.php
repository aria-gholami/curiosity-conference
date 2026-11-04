<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\QuestionnaireAnswer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireAnswer;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireAnswerRepository;
use CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer\Loader as LoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\AnswerCollection;
use Psr\Log\LoggerInterface;

final readonly class Loader implements LoaderInterface
{
    public function __construct(
        private QuestionnaireAnswerRepository $questionnaireAnswerRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(): AnswerCollection
    {
        try {
            $this->logger->info('Start loading all questionnaire answers.');
            $questionnaireAnswerGroup = $this->questionnaireAnswerRepository->findAllWithQuestions();
            $this->logger->info('Loading all questionnaire answers was successful.');

            return new AnswerCollection($questionnaireAnswerGroup);
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading all questionnaire answers was not successful.');
            $this->logger->debug('Loading all questionnaire answers was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(QuestionnaireAnswer::class);
        }
    }
}
