<?php

declare(strict_types=1);

namespace CuriosityConference\Application\Port\Outbound\QuestionnaireAnswer;

use CuriosityConference\Domain\Exception\LoadEntityFailedException;
use CuriosityConference\Domain\QuestionnaireQuestion\AnswerCollection;

interface Loader
{
    /**
     * @throws LoadEntityFailedException
     */
    public function load(): AnswerCollection;
}
