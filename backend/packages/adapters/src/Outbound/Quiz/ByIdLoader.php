<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz;
use CuriosityConference\Adapters\Outbound\Persistence\Repository\QuizRepository;
use CuriosityConference\Application\Port\Outbound\Quiz\ByIdLoader as ByIdLoaderInterface;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Quiz\Id;
use Psr\Log\LoggerInterface;

final readonly class ByIdLoader implements ByIdLoaderInterface
{
    public function __construct(
        private QuizRepository $quizRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function load(Id $id): Quiz
    {
        try {
            $this->logger->info('Start loading quiz by id.', [
                'id' => $id->value,
            ]);
            $quiz = $this->quizRepository->findOneBy(['id' => $id->value]);
            if (!$quiz instanceof Quiz) {
                throw LoadEntityFailedException::loadFailed(Quiz::class);
            }

            $this->logger->info('Loading quiz by id was successful.', [
                'id' => $id->value,
            ]);

            return $quiz;
        } catch (\Throwable $exception) {
            $this->logger->notice('Loading quiz by id was not successful.');
            $this->logger->debug('Loading quiz by id was not successful.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw LoadEntityFailedException::loadFailed(Quiz::class);
        }
    }
}
