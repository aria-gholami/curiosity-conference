<?php

declare(strict_types=1);

namespace App\Questionnaire;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use CuriosityConference\Core\Questionnaire\PageModifiedBackendSendHandler as PageModifiedBackendSendHandlerInterface;
use CuriosityConference\Core\Questionnaire\PageModifiedDataBackendSender;

final readonly class PageModifiedBackendSendHandler implements PageModifiedBackendSendHandlerInterface
{
    public function __construct(
        private PageModifiedDataBackendSender $pageModifiedDataBackendSender,
    ) {
    }

    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    #[\Override]
    public function handle(array $questions, bool $showResults): void
    {
        $postData = [
            'questions' => \array_map(function (array $question): array {
                return [
                    'question' => $question['questionTitle'],
                    'answers' => $this->mapAnswerData($question['answers']),
                ];
            }, $questions),
            'showResults' => $showResults,
        ];
        $this->pageModifiedDataBackendSender->post($postData);
    }

    private function mapAnswerData(array $answers): array
    {
        return \array_map(static function (array $answer): string {
            return $answer['answerTitle'];
        }, $answers);
    }
}
