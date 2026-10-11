<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Questionnaire;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;

interface PageModifiedDataBackendSender
{
    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    public function post(array $postData): void;
}
