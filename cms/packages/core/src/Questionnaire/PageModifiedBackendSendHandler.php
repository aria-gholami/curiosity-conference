<?php

declare(strict_types=1);

namespace CuriosityConference\Core\Questionnaire;

use CuriosityConference\Core\Questionnaire\Exception\CouldNotSendQuestionnaireModifiedDataException;

interface PageModifiedBackendSendHandler
{
    /**
     * @throws CouldNotSendQuestionnaireModifiedDataException
     */
    public function handle(array $questions): void;
}
