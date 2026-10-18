<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\QuestionnaireQuestion;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuestionnaireQuestionRepository;
use CuriosityConference\Application\Port\Outbound\QuestionnaireQuestion\QuestionResetter as QuestionResetterInterface;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use Psr\Log\LoggerInterface;

final readonly class QuestionResetter implements QuestionResetterInterface
{
    public function __construct(
        private QuestionnaireQuestionRepository $questionnaireQuestionRepository,
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
            $this->logger->info('Start resetting questionnaire questions.');
            $this->questionnaireQuestionRepository->reset();
            $this->logger->info('Resetting questionnaire questions was successful.');
        } catch (\Throwable $exception) {
            $this->logger->notice('Resetting questionnaire questions was not successful.');
            $this->logger->debug('Resetting questionnaire questions was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw DeleteFailedException::deleteFailed(QuestionnaireQuestion::class);
        }
    }
}
