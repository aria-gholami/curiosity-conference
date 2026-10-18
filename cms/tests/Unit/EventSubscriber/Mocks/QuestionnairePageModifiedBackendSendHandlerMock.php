<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber\Mocks;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use CuriosityConference\Core\Questionnaire\PageModifiedBackendSendHandler;

final class QuestionnairePageModifiedBackendSendHandlerMock implements PageModifiedBackendSendHandler
{
    public array $inputQuestions;
    public bool $inputShowResults;
    public ?CouldNotSendQuestionnaireModifiedDataException $throwCouldNotSendQuestionnaireModifiedDataException = null;

    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    #[\Override]
    public function handle(array $questions, bool $showResults): void
    {
        $this->inputQuestions = $questions;
        $this->inputShowResults = $showResults;
        if ($this->throwCouldNotSendQuestionnaireModifiedDataException instanceof CouldNotSendQuestionnaireModifiedDataException) {
            throw $this->throwCouldNotSendQuestionnaireModifiedDataException;
        }
    }
}
