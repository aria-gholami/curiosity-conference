<?php

declare(strict_types=1);

namespace App\Trivia;

use CuriosityConference\Core\Trivia\Exception\CouldNotSendTriviaModifiedDataException;
use CuriosityConference\Core\Trivia\PageModifiedBackendSendHandler as PageModifiedBackendSendHandlerInterface;
use CuriosityConference\Core\Trivia\PageModifiedDataBackendSender;

final readonly class PageModifiedBackendSendHandler implements PageModifiedBackendSendHandlerInterface
{
    public function __construct(
        private PageModifiedDataBackendSender $pageModifiedDataBackendSender,
    ) {
    }

    /**
     * @throws CouldNotSendTriviaModifiedDataException
     */
    #[\Override]
    public function handle(array $quizzes): void
    {
        $postData = \array_map(function (array $quiz) {
            return [
                'title' => $quiz['quizTitle'],
                'questions' => $this->mapQuestionData($quiz['questions']),
            ];
        }, $quizzes);

        $this->pageModifiedDataBackendSender->post($postData);
    }

    private function mapQuestionData(array $questions): array
    {
        return \array_map(function (array $question) {
            return [
                'question' => $question['question'],
                'answers' => $this->mapAnswerData($question['answers']),
            ];
        }, $questions);
    }

    private function mapAnswerData(array $answers): array
    {
        return \array_map(static function (array $answer) {
            return [
                'answer' => $answer['answer'],
                'correct' => $answer['correct'] ?? false,
            ];
        }, $answers);
    }
}
