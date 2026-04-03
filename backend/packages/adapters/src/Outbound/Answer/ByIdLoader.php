<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Answer;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Answer;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\AnswerRepository;
use CuriosityConference\Application\Port\Outbound\Answer\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private AnswerRepository $answerRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function load(string $id): Answer
    {
        try {
            $this->logger->info('Start loading answer by answer id.', [
                'answerId' => $id,
            ]);
            $answer = $this->answerRepository->findOneBy(['id' => $id]);
            if (!$answer instanceof Answer) {
                throw LoadEntityFailedException::loadFailed(Answer::class);
            }
            $this->logger->info('Loading answer by answer id was successful.', [
                'answerId' => $id,
            ]);

            return $answer;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading answer by answer id was not successful.', [
                'answerId' => $id,
            ]);
            $this->logger->debug('Loading answer by answer id was not successful.', [
                'answerId' => $id,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(Answer::class);
        }
    }
}
