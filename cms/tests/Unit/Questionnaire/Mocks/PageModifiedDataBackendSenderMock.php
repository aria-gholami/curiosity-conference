<?php

declare(strict_types=1);

namespace App\Tests\Unit\Questionnaire\Mocks;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;
use CuriosityConference\Core\Questionnaire\PageModifiedDataBackendSender;

final class PageModifiedDataBackendSenderMock implements PageModifiedDataBackendSender
{
    public array $inputPostData;
    public ?CouldNotSendQuestionnaireModifiedDataException $throwCouldNotSendQuestionnaireModifiedDataException = null;

    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    #[\Override]
    public function post(array $postData): void
    {
        $this->inputPostData = $postData;
        if ($this->throwCouldNotSendQuestionnaireModifiedDataException instanceof CouldNotSendQuestionnaireModifiedDataException) {
            throw $this->throwCouldNotSendQuestionnaireModifiedDataException;
        }
    }
}
