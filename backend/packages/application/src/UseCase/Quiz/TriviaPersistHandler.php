<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Quiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Quiz as QuizEntity;
use CuriosityConference\Application\Port\Inbound\Quiz\TriviaPersistHandler as TriviaPersistHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Question\ByQuizDeleter;
use CuriosityConference\Application\Port\Outbound\Question\Persister as QuestionPersister;
use CuriosityConference\Application\Port\Outbound\Quiz\ByIdDeleter;
use CuriosityConference\Application\Port\Outbound\Quiz\ByNameLoader;
use CuriosityConference\Application\Port\Outbound\Quiz\Loader;
use CuriosityConference\Application\Port\Outbound\Quiz\Persister;
use CuriosityConference\Domain\Answer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\Question\Question;
use CuriosityConference\Domain\Quiz\Id;
use CuriosityConference\Domain\Quiz\Quiz;

final readonly class TriviaPersistHandler implements TriviaPersistHandlerInterface
{
    public function __construct(
        private Loader $quizLoader,
        private ByIdDeleter $quizByIdDeleter,
        private ByNameLoader $byNameLoader,
        private Persister $quizPersister,
        private ByQuizDeleter $byQuizDeleter,
        private QuestionPersister $questionPersister,
    ) {
    }

    /**
     * @throws CorrectAnswerNotExistingException
     * @throws DeleteFailedException
     * @throws EmptyStringException
     * @throws PersistFailedException
     * @throws LoadEntityFailedException
     */
    #[\Override]
    public function handle(array $quizzes): void
    {
        try {
            $quizCollection = $this->quizLoader->load();
            $quizTitles = \array_column($quizzes, 'title');
            foreach ($quizCollection as $quizEntity) {
                if (!\in_array($quizEntity->getTitle(), $quizTitles, true)) {
                    $this->quizByIdDeleter->delete(new Id($quizEntity->getId()));
                }
            }
        } catch (LoadEntityFailedException) {
        }

        foreach ($quizzes as $quiz) {
            $quizEntity = $this->byNameLoader->load($quiz['title']);
            if (!$quizEntity instanceof QuizEntity) {
                $this->quizPersister->persist(new Quiz($quiz['title']));
                /** @var QuizEntity $quizEntity */
                $quizEntity = $this->byNameLoader->load($quiz['title']);
            }

            $this->byQuizDeleter->delete($quizEntity);
            $this->handleQuestions($quiz['questions'], $quizEntity);
        }
    }

    /**
     * @throws CorrectAnswerNotExistingException
     * @throws PersistFailedException
     */
    private function handleQuestions(array $questions, QuizEntity $quizEntity): void
    {
        foreach ($questions as $questionByRequest) {
            $correctAnswerIndex = $this->getCorrectAnswerIndex($questionByRequest['answers']);
            $question = new Question($quizEntity, $questionByRequest['question'], \array_column($questionByRequest['answers'], 'answer'), $correctAnswerIndex);
            $this->questionPersister->persist($question);
        }
    }

    /**
     * @throws CorrectAnswerNotExistingException
     */
    private function getCorrectAnswerIndex(array $answers): int
    {
        $correctAnswerIndex = null;
        foreach ($answers as $index => $answer) {
            /** @var ?bool $isCorrect */
            $isCorrect = $answer['correct'];
            if ($isCorrect ?? false) {
                $correctAnswerIndex = $index;
                break;
            }
        }
        if (!\is_int($correctAnswerIndex)) {
            throw new CorrectAnswerNotExistingException(
                'Question must have a correct answer.',
            );
        }

        return $correctAnswerIndex;
    }
}
