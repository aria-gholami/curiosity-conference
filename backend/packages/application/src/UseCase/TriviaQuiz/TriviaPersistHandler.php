<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\TriviaQuiz;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz as QuizEntity;
use CuriosityConference\Application\Port\Inbound\TriviaQuiz\TriviaPersistHandler as TriviaPersistHandlerInterface;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\ByQuizDeleter;
use CuriosityConference\Application\Port\Outbound\TriviaQuestion\Persister as QuestionPersister;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByIdDeleter;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\ByNameLoader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Loader;
use CuriosityConference\Application\Port\Outbound\TriviaQuiz\Persister;
use CuriosityConference\Domain\Exception\DeleteFailedException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\TriviaAnswer\Exception\CorrectAnswerNotExistingException;
use CuriosityConference\Domain\TriviaQuestion\Question;
use CuriosityConference\Domain\TriviaQuiz\Id;
use CuriosityConference\Domain\TriviaQuiz\Quiz;

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
