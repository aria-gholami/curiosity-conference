<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber\Mocks;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use CuriosityConference\Core\Questionnaire\PageModifiedBackendSendHandler;

final class QuestionnairePageModifiedBackendSendHandlerMock implements PageModifiedBackendSendHandler
{
    public array $inputQuestions;
    public ?CouldNotSendQuestionnaireModifiedDataException $throwCouldNotSendQuestionnaireModifiedDataException = null;

    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    #[\Override]
    public function handle(array $questions): void
    {
        $this->inputQuestions = $questions;
        if ($this->throwCouldNotSendQuestionnaireModifiedDataException instanceof CouldNotSendQuestionnaireModifiedDataException) {
            throw $this->throwCouldNotSendQuestionnaireModifiedDataException;
        }
    }
}
